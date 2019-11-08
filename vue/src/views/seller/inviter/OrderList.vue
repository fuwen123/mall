<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="分销业绩" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
                <div class="settle-item" v-for="item in order_list" :key="item.orderinviter_id">
                    <div class="title">{{item.orderinviter_member_name}}<span>{{$moment.unix(item.orderinviter_addtime).format('YYYY年MM月DD日')}}</span></div>
                    <div class="content">

                        {{item.orderinviter_remark}}
                    </div>
                    <div class="payment-account">
                        <span>{{item.orderinviter_money}}</span>
                        <mt-button class="btn" type="default" size="small" disabled>{{item.orderinviter_valid?'有效':'无效'}}</mt-button>
                    </div>
                </div>
        </div>
        <empty-record v-if="order_list && !order_list.length"></empty-record>
    </div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getInviterOrderList } from '../../../api/sellerInviter'
export default {
  name: 'OrderList',
  components: {
    EmptyRecord
  },
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      // 分销商品列表
      order_list: false
    }
  },
  created: function () {
    //this.getOrderList()
  },
  methods: {
    getOrderList (ispush) {
      Indicator.open()
      let params = this.params
      getInviterOrderList(params).then(res => {
        Indicator.close()

            if (ispush && this.order_list) {
              this.order_list = this.order_list.concat(res.result.order_list)
            } else {
              this.order_list = res.result.order_list
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
        this.getOrderList(true)
      }
    }
  }
}
</script>

<style scoped lang="scss">
    .settle-item {
        background: #fff;
        margin-top: .5rem;
        border-top: 1px solid #eee;
        border-bottom: 1px solid #eee;
        padding: 0 .5rem;
    .title {
        font-size: .6rem;
        border-bottom: 1px solid #eee;
        color: #999;
        line-height: 2;
        span{float:right}
    .settle-state {
        float: right;
    }
    .state-check {
        color: orangered
    }
    }
    .content {
        padding: .5rem 0;
        display: flex;
        line-height: 1.5;
        font-size:.8rem;
    .item {
        flex: 1;
    .mt {
        font-size: .6rem;
        color: #999
    }
    .mc {
        font-size: .6rem;
        color: #000;

    .strong {
        font-size: .7rem;
    }
    }
    .mc.income {
        color: #4caf50
    }
    .mc.outlay {
        color: #f31c47
    }
    }

    }
    .payment-account{font-size:.7rem;color:#333;padding:.5rem 0;border-top:1px solid #eee;display:flex;
    span{flex:1;line-height:1.65rem;padding-right:.5rem}
    .btn{width:4rem}
    }
    }
</style>
