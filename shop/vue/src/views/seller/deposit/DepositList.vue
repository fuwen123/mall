<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="资金记录" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div class="mt-5" v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
            <div class="item mb-5" v-for="item in logList" :key="item.log_id">
                <div class="mt-wrapper">
                    <div class="mt" v-if="item.store_avaliable_deposit !== '0.00'">
                        <h2>已缴保证金：{{item.store_avaliable_deposit}}</h2>
                    </div>
                    <div class="mt" v-if="item.store_freeze_deposit!== '0.00'">
                        <h2>审核保证金：{{item.store_freeze_deposit}}</h2>
                    </div>
                    <div class="mt" v-if="item.store_payable_deposit!== '0.00'">
                        <h2>应缴保证金：{{item.store_payable_deposit}}</h2>
                    </div>
                </div>
                <div class="mc">
                    {{item.storedepositlog_desc}}
                </div>
                <div class="time"> {{ $moment.unix(item.storedepositlog_add_time).format('YYYY-MM-DD h:mm:ss') }}</div>
            </div>
            <empty-record v-if="logList && !logList.length"></empty-record>
        </div>
    </div>
</template>

<script>
import { Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getSellerDepositList } from '../../../api/sellerDeposit'
export default {
  name: 'LogList',
  components: {
    EmptyRecord
  },
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      logList: false // 记录列表
    }
  },
  methods: {
    getLogList (ispush) {
      Indicator.open()
      let params = this.params
      getSellerDepositList(params).then(res => {
        Indicator.close()

            if (ispush && this.logList) {
              this.logList = this.logList.concat(res.result.log_list)
            } else {
              this.logList = res.result.log_list
            }
            if (res.result.hasmore) {
              this.isMore = true
            } else {
              this.isMore = false
            }

      })
    },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getLogList(true)
      }
    }
  }
}
</script>
<style scoped>
    .item{padding:.5rem;background:#fff;}
    .item .mt-wrapper{display: flex}
    .item .mt{height:2rem;line-height:2rem;flex:1}
    .item .mt h2{font-weight:bold;font-size:.7rem}
    .item .time{font-size:.6rem;color:gray}
    .item .mc{padding-top:.5rem;line-height:1rem;font-size:.8rem;}
</style>
