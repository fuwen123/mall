<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="消费记录" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div class="mt-5" v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
            <div class="item mb-5" v-for="item in costList" :key="item.cost_id">
                <div class="info">
                    <p class="content">{{item.storecost_remark}}<em class="ml-5">({{storecost_state(item.storecost_state)}})</em></p>
                    <p class="time">{{ $moment.unix(item.storecost_time).format('YYYY-MM-DD h:mm:ss') }}</p>
                </div>
                <div class="price">{{item.storecost_price}}</div>
            </div>
        </div>
    </div>
</template>

<script>
import { Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getSellerCostList } from '../../../api/seller'
export default {
  name: 'CostList',
  components: {
    EmptyRecord
  },
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      costList: false // 记录列表
    }
  },
  computed: {
    storecost_state () {
      return function (state) {
        if (state === 0) {
          return '未结算'
        } else {
          return '已结算'
        }
      }
    }
  },
  methods: {
    getCostList (ispush) {
      Indicator.open()
      let params = this.params
      getSellerCostList(params).then(res => {
        Indicator.close()

            if (ispush && this.costList) {
              this.costList = this.costList.concat(res.result.cost_list)
            } else {
              this.costList = res.result.cost_list
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
        this.getCostList(true)
      }
    }
  }
}
</script>

<style scoped>
    .item{padding:.5rem;background:#fff;height:3rem;}
    .item .info{float:left;width:80%;}
    .item .info p{line-height:1.5rem;font-size:.8rem;}
    .item .info .content em{}
    .item .price{float:left;width:20%;line-height:3rem;text-align:right;color:red;font-weight:bold;}
</style>
