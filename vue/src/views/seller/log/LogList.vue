<template>
    <div class="container">
    <div class="common-header-wrap">
        <mt-header title="账号日志" class="common-header">
            <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
    </div>
        <div class="mt-5" v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
            <div class="item mb-5" v-for="item in logList" :key="item.log_id">
                <div class="mt">
                    <h2>{{item.sellerlog_seller_name}}</h2><em class="time"> {{ $moment.unix(item.sellerlog_time).format('YYYY-MM-DD h:mm:ss') }}</em>
                </div>
                <div class="mc">
                    {{item.sellerlog_content}}
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getSellerLogList } from '../../../api/seller'
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
      getSellerLogList(params).then(res => {
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
.item .mt{height:1rem;line-height:1rem;}
.item .mt h2{float:left;font-size:0.7rem;font-weight:600;}
.item .mt em{float:right;font-size:0.6rem}
.item .mc{padding-top:.5rem;line-height:1rem;font-size:.5rem;color:#999}
</style>
