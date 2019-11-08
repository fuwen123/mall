<template>
<div class="container">
    <div class="common-header-wrap">
        <mt-header title="代金券" class="common-header">
            <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
    </div>
    <div class="common-popup-content">
        <div class="common-voucher common-voucher02"  v-for="(item,index) in voucher" :key="item.vouchertemplate_id"  @click="receiveVoucher(item.vouchertemplate_id)">
            <div class="par"><p>订单满{{item.vouchertemplate_limit}}元</p><sub class="sign">￥</sub><span>{{item.vouchertemplate_price}}</span></div>
            <div class="copy">有效期至<p><br>{{$moment.unix(item.vouchertemplate_enddate).format('YYYY年MM月DD日')}}</p></div>
            <i></i>
        </div>
    </div>
</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import { Toast } from 'mint-ui'
import { receiveVoucher } from '../../../api/homestoredetail'
export default {
  name: 'HomeStoreVoucher',
  data () {
    return {

    }
  },
  components: {

  },
  computed: {
    ...mapState({
      voucher: state => state.goodsdetail.voucher
    })
  },
  created () {

  },
  methods: {

    receiveVoucher (tid) {
      receiveVoucher(
        tid
      ).then((res) => {
        Toast(res.message)
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  }

}
</script>

<style scoped>
    .common-voucher{margin:.5rem auto}
</style>
