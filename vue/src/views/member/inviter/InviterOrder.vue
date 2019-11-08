<template>
  <div class="inviter-order-list">
    <div class="common-header-wrap">
      <mt-header title="推广佣金" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
      </mt-header>
    </div>
    <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
      <div  v-if="orderList && orderList.length">
        <div class="inviter-item" v-for="(v,k) in orderList" :key="v.orderinviter_id">
          <div :class="{invalid:!v.orderinviter_valid}">
          <div class="detail">{{v.orderinviter_remark}}</div>
          <div class="money add">+{{v.orderinviter_money}}</div>
          <time class="date">
            <span class="green" v-if="v.orderinviter_valid">有效</span>
            <span class="gray" v-else>无效</span>
          </time>
          </div>
        </div>
      </div>
      <empty-record v-else-if="orderList && !orderList.length"></empty-record>
    </div>
  </div>
</template>

<script>
import { getInviterOrder } from '../../../api/memberInviter'
import EmptyRecord from '../../../components/EmptyRecord'
import { Toast, Indicator } from 'mint-ui'
export default {
  name: 'MemberInviterOrder',
  components: {
    EmptyRecord
  },
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      orderList: false
    }
  },
  created () {

  },

  mounted () {

  },
  methods: {
    getOrderList (ispush) {
      Indicator.open()
      let params = this.params
      getInviterOrder(params).then(res => {
        Indicator.close()
        if (res) {
          if (ispush && this.orderList) {
            this.orderList = this.orderList.concat(res.result.list)
          } else {
            this.orderList = res.result.list
          }
          if (res.result.hasmore) {
            this.isMore = true
          } else {
            this.isMore = false
          }
        }
      })
    },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getOrderList(true)
      }
    }
  }
}
</script>

<style scoped lang="scss">
  .inviter-order-list {}
  .inviter-order-list .inviter-item {  background-color: #FFF;position: relative; z-index: 1; display: block; padding: 0.5rem 0; border-bottom: solid 0.05rem #EEE;}
  .inviter-order-list .inviter-item .detail { min-height: 1.8rem; margin: 0 5rem 0 0; font-size: 0.55rem; word-wrap:break-word; line-height: 0.9rem; color: #555;padding-left:.5rem}
  .inviter-order-list .inviter-item .money { position: absolute; z-index: 1; top: 0.5rem; right: 0.5rem; height: 0.9rem; line-height: 0.9rem;}
  .inviter-order-list .inviter-item .money.add { color: #f23030;}
  .inviter-order-list .inviter-item .money.reduce { color: #36BC9B;}
  .inviter-order-list .inviter-item .date { position: absolute; z-index: 1; bottom: 0.4rem; right: 0.5rem; height: 0.9rem; font-size: 0.5rem; line-height: 0.9rem; color: #999;}
  .inviter-order-list .inviter-item .date .green{color:green}
  .inviter-order-list .inviter-item .invalid .money.add {color: gray;}
</style>
