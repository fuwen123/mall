<template>
<div class="container">
    <div class="common-header-wrap">
        <mt-header title="确认订单" class="common-header">
            <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
    </div>
    <div class="body" v-if="store_final_total_list">
        <checkout-address class="address" v-on:onclick="goAddress" :item="store_final_total_list.address_info"></checkout-address>
        <!--发票-->
        <div class="mt-5" @click="$router.push({name:'MemberInvoiceList',query:{isFromCheckout:true,goBackLevel:-1,invoice_id:invoice_id,vat_deny:store_final_total_list.vat_deny,params:JSON.stringify($route.query)}})"><mt-cell class="menu-item" title="发票" ><span slot="icon" class="left-icon iconfont">&#xe6f2;</span>{{invoice_content}}<i class="indicator iconfont">&#xe650;</i></mt-cell></div>
        <checkout-store class="section-header" :finalTotalList="store_final_total_list.store_final_total_list" :addressApi="store_final_total_list.address_api" :storeCartList="store_final_total_list.store_cart_list" @selectVoucher="selectVoucher" @selectPay="selectPay" @changeMessage="changeMessage"></checkout-store>
    </div>
    <div class="bottom-wrapper" v-if="store_final_total_list">
        <mt-field v-if="!f_code_check && store_final_total_list.store_cart_list_api[0].goods_list[0].is_goodsfcode==1" label="F码" placeholder="请填写F码" type="text" v-model="f_code"><mt-button size="small" type="danger" @click="checkfcode">使用</mt-button></mt-field>
        <div v-else class="amount-wrapper">
            <label class="amount ml-5" v-if="store_final_total_list"><mt-switch v-model="pd_pay" v-if="store_final_total_list.available_predeposit>0">预存款</mt-switch><mt-switch v-model="rcb_pay" v-if="store_final_total_list.available_rc_balance>0">充值卡</mt-switch></label>
            <mt-button size="small" type="danger" @click="checkout">提交订单</mt-button>
        </div>
    </div>
</div>
</template>

<script>
import { Toast, MessageBox } from 'mint-ui'
import { buyStep1, buyStep2, checkFCode } from '../../../api/memberBuy'
import CheckoutAddress from './child/CheckoutAddress'
import CheckoutItem from './child/CheckoutItem'
import CheckoutStore from './child/CheckoutStore'

export default {
  name: 'Step1',
  components: {
    CheckoutAddress,
    CheckoutItem,
    CheckoutStore
  },
  data () {
    return {
      pay_code: 'online',
      f_code: '',
      invoice_id: this.$route.query.invoice_id ? this.$route.query.invoice_id : 0,
      invoice_content: this.$route.query.invoice_content ? this.$route.query.invoice_content : '',
      f_code_check: false,
      cart_id: this.$route.query.cart_id ? this.$route.query.cart_id : '',
      store_final_total_list: null,
      pd_pay: false,
      rcb_pay: false,
      password: '',
      message: []
    }
  },
  created: function () {
    this.getBuyInfo()
  },
  methods: {
    checkfcode () {
      checkFCode(this.store_final_total_list.store_cart_list_api[0].goods_list[0].goods_commonid, this.f_code).then(res => {
        this.f_code_check = true
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    // 获取订单信息
    getBuyInfo () {
      let extra = {}
      if (this.$route.query.bargainorder_id) {
        extra['bargainorder_id'] = this.$route.query.bargainorder_id
      }
      buyStep1(
        this.cart_id, this.$route.query.buy_now ? 0 : 1, this.$route.query.pintuan_id, this.$route.query.pintuangroup_id, extra
      ).then((res) => {
        if (res.code === 10000) {
          this.store_final_total_list = res.result
          if (this.invoice_content) {
            if (!this.store_final_total_list.inv_info.invoice_id) {
              this.invoice_id = 0
              this.invoice_content = this.store_final_total_list.inv_info.content
            }
          } else {
            this.invoice_content = this.store_final_total_list.inv_info.content
            if (this.store_final_total_list.inv_info.invoice_id) {
              this.invoice_id = this.store_final_total_list.inv_info.invoice_id
            }
          }
        } else {

        }
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    goBack () {
      this.$router.go(-1)
    },
    selectVoucher (voucherInfo,store_id) {
      this.store_final_total_list.store_cart_list[store_id].store_voucher_info = voucherInfo
    },
    selectPay (payCode) {
      this.pay_code = payCode
    },
    goAddress () {
      if (this.store_final_total_list.address_info) {
        this.$router.push({ name: 'MemberAddressList' })
      } else {
        this.$router.push({
          name: 'MemberAddressForm',
          query: {
            action: 'add',
            isFromCheckout: true,
            goBackLevel: -1
          }
        })
      }
    },
    changeMessage (message, store_id) {
      this.message[store_id] = message
    },
    // 提交订单
    checkout () {
      let ifcart = this.$route.query.buy_now ? 0 : 1
      let pintuan_id = this.$route.query.pintuan_id
      let pintuangroup_id = this.$route.query.pintuangroup_id
      let cart_id = this.cart_id
      let address_id = this.store_final_total_list.address_info ? this.store_final_total_list.address_info.address_id : 0
      let vat_hash = this.store_final_total_list.vat_hash
      let offpay_hash = this.store_final_total_list.address_api.offpay_hash
      let offpay_hash_batch = this.store_final_total_list.address_api.offpay_hash_batch
      let invoice_id = this.invoice_id
      let voucher = null
      let pd_pay = this.pd_pay ? 1 : 0
      let password = this.password
      let rcb_pay = this.rcb_pay ? 1 : 0
      let pay_message_temp = []
      let f_code = this.f_code
      let pay_code = this.pay_code
      if (!address_id) {
        MessageBox.alert('您需要先去添加收货地址', '')
        return
      }
      if (pd_pay || rcb_pay) {
        if (!this.store_final_total_list.member_paypwd) {
          MessageBox.confirm('您需要先去设置支付密码', '').then(action => {
            this.$router.push({ name: 'MemberSetting' })
          })
          return
        } else {
          if (!password) {
            MessageBox.prompt('请输入支付密码', '', {
              showCancelButton: true,
              inputType: 'password'

            }).then(({ value, action }) => {
              this.password = value
              this.checkout()
            })
            return
          }
        }
      }
      // 处理格式
      voucher = ''
      let voucher_temp = []
      for (var t in this.store_final_total_list.store_cart_list) {
        voucher_temp.push([this.store_final_total_list.store_cart_list[t].store_voucher_info.vouchertemplate_id + '|' + t + '|' + this.store_final_total_list.store_cart_list[t].store_voucher_info.voucher_price])
      }
      voucher = voucher_temp.join(',')
      let i
      for (i in this.store_final_total_list.store_cart_list) {
        let message = this.message[this.store_final_total_list.store_cart_list[i].store_id]
        if (typeof (message) === 'undefined') {
          message = ''
        }
        pay_message_temp.push(this.store_final_total_list.store_cart_list[i].store_id + '|' + message)
      }
      let pay_message = pay_message_temp.join(',')
      let extra = {}
      if (this.$route.query.bargainorder_id) {
        extra['bargainorder_id'] = this.$route.query.bargainorder_id
      }
      buyStep2(
        ifcart,
        cart_id,
        address_id,
        vat_hash,
        offpay_hash,
        offpay_hash_batch,
        invoice_id,
        voucher,
        pd_pay,
        password,
        rcb_pay,
        pay_message,
        pintuan_id,
        pintuangroup_id,
        f_code,
        pay_code,
        extra
      ).then((res) => {
        // 跳转到支付界面
        let pay_sn = res.result.pay_sn
        this.$router.push({ name: 'MemberBuyPay', query: { pay_sn: pay_sn, pay_type: 'pay_new' } })
      }).catch(function (error) {
        Toast(error.message)
      })
    }

  }
}
</script>

<style scoped lang="scss">
    .container {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
    }
    .header {
        height: 2.2rem;
        color: #48505d;
        font-size: 0.9rem;
        background-color: #fff;
        padding: 0.1rem 0;
        border-bottom: 1px solid #e8eaed;
    }
    .body {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        margin-bottom:4rem;
    }
    .address {
        height: 5rem;
    }
    .goods {
        height: 4.5rem;
    }
    .item {
        height: 2.5rem;
    }
    .section-header {
        margin-top: 0.4rem;
    }
    .section-footer {
        margin-bottom: 0.4rem;
    }
    .comment {
        height: 7.25rem;
    }
    .desc {
        background-color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        padding-top: 0.5rem;
        padding-bottom: 0.5rem;
    }
    .desc-item {
        height: 1.5rem;
    }
    .bottom-wrapper {
        z-index: 1;
        background: #fff;
        position: fixed;
        left: 0;
        right: 0;
        bottom: 0;
        min-height: 2.5rem;
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: stretch;
    }
    .amount-wrapper {
        flex: 1;
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
        background-color: #fff;
    }
    .amount {
        flex: 1;
        font-size: 0.8rem;
        color: $mainColor;
        text-align: right;
        padding-right: 0.75rem;
    }
    .submit {
        width: 7.5rem;
        height: 2.5rem;
        border-radius: 0;
    }
</style>
