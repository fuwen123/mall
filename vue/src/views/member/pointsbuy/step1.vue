<template>
<div class="container">
    <div class="common-header-wrap">
        <mt-header title="确认订单" class="common-header">
            <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
    </div>
    <div class="body" v-if="store_final_total_list">
        <checkout-address class="address" v-on:onclick="goAddress" :item="store_final_total_list.address_info"></checkout-address>
        <checkout-store class="section-header" :totalAmount="store_final_total_list.pointprod_arr.pgoods_pointall" :storeCartList="store_final_total_list.pointprod_arr.pointprod_list" @changeMessage="changeMessage"></checkout-store>
    </div>
    <div class="bottom-wrapper" v-if="store_final_total_list">
        <div class="amount-wrapper">
            <label class="amount ml-5" ></label>
            <mt-button size="small" type="danger" @click="checkout">提交订单</mt-button>
        </div>
    </div>
</div>
</template>

<script>
import { Toast, MessageBox } from 'mint-ui'
import { buyStep1, buyStep2 } from '../../../api/memberPointscart'
import CheckoutAddress from './child/CheckoutAddress'
import CheckoutItem from './child/CheckoutItem'
import CheckoutStore from './child/CheckoutStore'

export default {
  name: 'Step1',
  components: {
    CheckoutAddress,
    // CheckoutItem,
    CheckoutStore
  },
  data () {
    return {
      cart_id: this.$route.query.cart_id ? this.$route.query.cart_id : '',
      store_final_total_list: null,

      message: ''
    }
  },
  created: function () {
    this.getBuyInfo()
  },
  methods: {
    // 获取订单信息
    getBuyInfo () {
      buyStep1(
        this.cart_id, this.$route.query.buy_now ? 0 : 1
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
    changeMessage (message) {
      this.message = message
    },
    // 提交订单
    checkout () {
      let ifcart = this.$route.query.buy_now ? 0 : 1
      let cart_id = this.cart_id
      let address_id = this.store_final_total_list.address_info ? this.store_final_total_list.address_info.address_id : 0
      let message = this.message

      if (!address_id) {
        MessageBox.alert('您需要先去添加收货地址', '')
        return
      }

      buyStep2(
        cart_id,
        ifcart,
        address_id,
        message
      ).then((res) => {
        this.$router.push({ name: 'MemberPointsOrderList' })
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
        height:2.2rem;
        color: #48505d;
        font-size:0.9rem;
        background-color: #fff;
        padding:0.1rem 0;
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
        height:5rem;
    }
    .goods {
        height:4.5rem;
    }
    .item {
        height:2.5rem;
    }
    .section-header {
        margin-top:0.4rem;
    }
    .section-footer {
        margin-bottom:0.4rem;
    }
    .comment {
        height:7rem;
    }
    .desc {
        background-color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        padding-top:0.5rem;
        padding-bottom: 0.5rem;
    }
    .desc-item {
        height:1.5rem;
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
        font-size:0.8rem;
        color: $primaryColor;
        text-align: right;
        padding-right:0.75rem;
    }
    .submit {
        width:7.5rem;
        height:2.5rem;
        border-radius: 0;
    }
</style>
