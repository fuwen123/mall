<template>
<div class="container">
    <div class="common-header-wrap">
        <mt-header title="确认订单" class="common-header">
            <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
    </div>
    <div class="body" v-if="store_final_total_list">
        <mt-field label="手机号" placeholder="请填写手机号" type="text" v-model="buyer_phone"></mt-field>
        <checkout-store class="section-header" :storeCartList="store_final_total_list.goods_info" @selectVoucher="selectVoucher" @changeMessage="changeMessage"></checkout-store>
    </div>
    <div class="bottom-wrapper" v-if="store_final_total_list">
        <div class="amount-wrapper">
            <label class="amount ml-5" v-if="store_final_total_list"><mt-switch v-model="pd_pay" v-if="store_final_total_list.member_info.available_predeposit>0">预存款</mt-switch><mt-switch v-model="rcb_pay" v-if="store_final_total_list.member_info.available_rc_balance>0">充值卡</mt-switch></label>
            <mt-button size="small" type="danger" @click="checkout">提交订单</mt-button>
        </div>
    </div>
</div>
</template>

<script>
import { Toast, MessageBox } from 'mint-ui'
import { buyStep1, buyStep2 } from '../../../api/memberVrBuy'
import CheckoutItem from './child/CheckoutItem'
import CheckoutStore from './child/CheckoutStore'

export default {
  name: 'Step1',
  components: {
    CheckoutItem,
    CheckoutStore
  },
  data () {
    return {
      goods_id: this.$route.query.goods_id ? this.$route.query.goods_id : '',
      quantity: this.$route.query.quantity ? this.$route.query.quantity : '',
      buyer_phone: '',
      store_final_total_list: null,
      pd_pay: false,
      rcb_pay: false,
      password: '',
      message: ''
    }
  },
  created: function () {
    this.getBuyInfo()
  },
  methods: {
    // 获取订单信息
    getBuyInfo () {
      let extra = {}
      if (this.$route.query.pintuan_id) {
          extra['pintuan_id'] = this.$route.query.pintuan_id
          extra['pintuangroup_id'] = this.$route.query.pintuangroup_id
      }
      buyStep1(
        this.goods_id, this.quantity, extra
      ).then((res) => {
        if (res.code === 10000) {
          this.store_final_total_list = res.result
        } else {

        }
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    goBack () {
      this.$router.go(-1)
    },
    selectVoucher (voucherInfo) {
      this.store_final_total_list.goods_info.store_voucher_info = voucherInfo
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
      this.message = message
    },
    // 提交订单
    checkout () {
      let goods_id = this.goods_id
      let quantity = this.quantity
      let buyer_phone = this.buyer_phone
      let buyer_msg = this.message
      let pd_pay = this.pd_pay ? 1 : 0
      let password = this.password
      let rcb_pay = this.rcb_pay ? 1 : 0

      if (!buyer_phone) {
        MessageBox.alert('您需要先去填写手机号', '')
        return
      }
      if (pd_pay || rcb_pay) {
        if (!this.store_final_total_list.member_info.member_paypwd) {
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
      let extra = {}
      if (this.$route.query.pintuan_id) {
          extra['pintuan_id'] = this.$route.query.pintuan_id
          extra['pintuangroup_id'] = this.$route.query.pintuangroup_id
      }
      // 处理格式
      let voucher = this.store_final_total_list.goods_info.store_voucher_info.vouchertemplate_id + '|' + this.store_final_total_list.goods_info.store_id + '|' + this.store_final_total_list.goods_info.store_voucher_info.voucher_price
      extra['voucher'] = voucher
      buyStep2(goods_id, quantity, buyer_phone, buyer_msg, pd_pay, password, rcb_pay, extra
      ).then((res) => {
        // 跳转到支付界面
        let pay_sn = res.result.order_sn
        this.$router.push({ name: 'MemberBuyPay', query: { pay_sn: pay_sn, pay_type: 'vr_pay_new' } })
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
        color: $primaryColor;
        text-align: right;
        padding-right: 0.75rem;
    }
    .submit {
        width: 7.5rem;
        height: 2.5rem;
        border-radius: 0;
    }
</style>
