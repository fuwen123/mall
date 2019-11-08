<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header title="代金券列表" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
      </mt-header>
    </div>
<div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
    <div
      class="list-wrapper"
      v-if="voucher_list && voucher_list.length"
    >
      <div class="common-voucher common-voucher02" :class="(item.voucher_state != 1)?'disable':''" v-for="(item,index) in voucher_list" :key="item.voucher_id">
        <div class="par"><p>订单满{{item.voucher_limit}}元</p><sub class="sign">￥</sub><span>{{item.voucher_price}}</span></div>
        <div class="copy">有效期至<p>{{item.voucher_end_date_text}}<br></p></div>
        <i></i>
      </div>

    </div>
    <empty-record v-else-if="voucher_list && !voucher_list.length"></empty-record>
  </div>
  </div>
</template>

<script>
import { getVoucherList } from '../../../api/memberVoucher'
import { Toast, Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
export default {
  name: 'BalanceHistory',
  components: {
    EmptyRecord
  },
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      voucher_list: false,
    }
  },
  created () {
    
  },

  mounted () {
    this.wrapperHeight = document.documentElement.clientHeight - 90
  },
  methods: {
    goBack () {
      this.$router.go(-1)
    },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getVoucherList(true)
      }
    },
    getVoucherList () {
      Indicator.open()



          getVoucherList(this.params).then(res => {
            Indicator.close()
            if (res.result.hasmore) {
              this.isMore = true
            } else {
              this.isMore = false
            }

            if (this.voucher_list) {
              this.voucher_list = this.voucher_list.concat(res.result.voucher_list)
            } else {
              this.voucher_list = res.result.voucher_list
            }

          }).catch(function (error) {
            Indicator.close()
            Toast(error.message)
          })

    }
  }
}
</script>

<style scoped lang="scss">
.container {
  height: 100%;
  display: flex;
  position: relative;
  flex-direction: column;
  justify-content: flex-start;
  align-items: stretch;

}
.common-voucher{margin:0.5rem auto}
</style>
