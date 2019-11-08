<!-- OrderDetailBody.vue -->
<template>
    <div class="order-deliver">
        <div class="common-header-wrap">
            <mt-header title="物流跟踪" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div>
            <mt-cell title="物流公司" :value="express_name" />
            <mt-cell title="物流单号" :value="shipping_code" />
        </div>
        <div class="mt-5" v-if="deliver_info">
            <mt-cell v-for="(item,index) in deliver_info" :key="index" :title="item.time" :value="item.context" />
        </div>
    </div>
</template>

<script>

import { Toast, MessageBox } from 'mint-ui'
import { getRefundDeliver } from '../../../api/sellerRefund'
export default {
  name: 'SellerRefundDeliver',
  data () {
    return {
        deliver_info: false,
        express_name:'',
        shipping_code:''
    }
  },

  created: function () {
    if (this.$route.query.refund_id) {
        getRefundDeliver(this.$route.query.refund_id).then(res => {
        this.deliver_info = res.result.deliver_info
            this.express_name=res.result.express_name
            this.shipping_code=res.result.shipping_code
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  },

  methods: {

  }
}
</script>
<style lang="scss" >
.order-deliver .mint-cell-title{width:5rem;flex:unset}
.order-deliver .mint-cell-value{flex:1}
</style>

