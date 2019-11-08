<template>
<div class="container">
    <div class="common-header-wrap">
        <mt-header title="账单管理" class="common-header">
            <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
    </div>
    <!-- header -->
    <div class="order-header">
        <ul>
            <li
                    class="item"
                    v-for="item in orderNav"
                    v-bind:key="item.id"
                    v-bind:class="{ active: bill_state == item.id }"
                    v-on:click="setOrderNavActive(item.id)"
            >
                {{ item.name }}
            </li>
        </ul>
    </div>
    <div class="mt-30" v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
        <div  v-if="billList && billList.length">
            <div class="settle-item" v-for="bill in billList" :key="bill.ob_no">
                <div class="title">{{$moment.unix(bill.ob_startdate).format('YYYY年MM月DD日')}}~{{$moment.unix(bill.ob_enddate).format('YYYY年MM月DD日')}}</div>
                <div class="content">

                    <div class="item">
                        <div class="mt">订单金额</div>
                        <div class="mc income"><span class="strong">{{parseFloat(bill.ob_order_totals)}}</span>元</div>
                    </div>
                    <div class="item">
                        <div class="mt">退还佣金</div>
                        <div class="mc income"><span class="strong">{{parseFloat(bill.ob_commis_return_totals)}}</span>元</div>
                    </div>
                    <div class="item">
                        <div class="mt">退单金额</div>
                        <div class="mc outlay"><span class="strong">{{parseFloat(bill.ob_order_return_totals)}}</span>元</div>
                    </div>
                    <div class="item">
                        <div class="mt">佣金金额</div>
                        <div class="mc outlay"><span class="strong">{{parseFloat(bill.ob_commis_totals)}}</span>元</div>
                    </div>
                    <div class="item">
                        <div class="mt">分销佣金</div>
                        <div class="mc outlay"><span class="strong">{{parseFloat(bill.ob_inviter_totals)}}</span>元</div>
                    </div>
                    <div class="item">
                        <div class="mt">促销费用</div>
                        <div class="mc outlay"><span class="strong">{{parseFloat(bill.ob_store_cost_totals)}}</span>元</div>
                    </div>
                </div>
                <div class="payment-account">
                    <span>应收：{{parseFloat(bill.ob_result_totals)}}</span>
                    <mt-button v-if="bill.ob_state==1" @click="confirmBill(bill.ob_no)" class="btn" type="default" size="small" plain>确认</mt-button>
                    <mt-button v-else class="btn" type="default" size="small" disabled>{{bill.ob_states}}</mt-button>
                </div>
            </div>
        </div>
        <empty-record v-else-if="billList && !billList.length"></empty-record>
    </div>
</div>
</template>

<script>
import { Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getBillList,confirmBill } from '../../../api/sellerBill'
export default {
  name: 'BillList',
  components: {
    EmptyRecord
  },
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      billList: false, // 商品列表
      ob_no: '',
      bill_state: '',
      orderNav: [
        {
          'name': '全部',
          'id': ''
        },
        {
          'name': '已出账',
          'id': '1'
        },
        {
          'name': '已确认',
          'id': '2'
        },
        {
          'name': '已完成',
          'id': '4'
        }
      ]
    }
  },
  methods: {
    setOrderNavActive (index) {
      this.bill_state = index
      this.reload()
    },
    confirmBill (ob_no) {
      confirmBill(ob_no).then(res => {
        Toast(res.message)
        this.$router.go(0)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    getBillList (ispush) {
      Indicator.open()
      let params = this.params
      getBillList(params, this.ob_no, this.bill_state).then(res => {
        Indicator.close()

            if (ispush && this.billList) {
              this.billList = this.billList.concat(res.result.bill_list)
            } else {
              this.billList = res.result.bill_list
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
        this.getBillList(true)
      }
    },
    reload () {
      // 重新加载数据
      this.params.page = 0
      this.isMore = true
      this.billList = []
      this.loadMore()
    }
  }
}
</script>

<style scoped lang="scss">
    .order-header {
        position: fixed;
        height: 2.2rem;
        width: 100%;
        top: 2.2rem;
        z-index: 100;
    ul {
        list-style: none;
        width: auto;
        display: flex;
        justify-content: space-around;
        align-content: center;
        align-items: center;
        height: 100%;
        background: rgba(255, 255, 255, 1);
        border-bottom: 1px solid #e8eaed;
    li {
        font-size: 0.7rem;
        color: #333;
        height: 100%;
        text-align: center;
        line-height: 2.2rem;
        border-bottom: 0.1rem solid transparent;
    &.active {
         color: $primaryColor;
         border-bottom-color: $primaryColor;
     }
    }
    }
    }
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
