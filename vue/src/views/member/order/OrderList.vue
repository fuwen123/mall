<template>
	<div class="member-order-list">
		<div class="common-header-wrap">
			<mt-header title="订单列表" class="common-header">
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
						v-bind:class="{ active: stateType == item.id }"
						v-on:click="setOrderNavActive(item.id)"
				>
					{{ item.name }}
				</li>
			</ul>
		</div>
		<div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
		<div class="order-body" v-if="order_group_list && order_group_list.length">
			<div v-for="(order_group, index) in order_group_list" v-bind:key="index">
				<div class="list" v-for="item in order_group.order_list" :key="item.order_id">
					<router-link :to="'/member/order_detail?order_id='+item.order_id">
						<div class="tips-body">
							<span class="tips"> 订单编号: {{ item.order_sn }} </span>
							<span class="title tips statusTips" >{{ item.state_desc }}</span>
						</div>
						<div class="order-image" v-if="item.extend_order_goods.length > 0" >
							<img v-bind:src="image.goods_image_url" v-for="image in item.extend_order_goods" />
						</div>
						<div class="price">
							(共计{{ item.extend_order_goods.length }}种商品) 合计 : ￥ <i>{{ item.order_amount }}</i><i class="freight">(运费:￥{{ item.shipping_fee }})</i>
						</div>
					</router-link>
					<div class="order-list-opratio">
						<!-- 锁定-->
						<div class="btn">
							<button v-if="item.if_lock">退款中</button>
						<!-- 取消订单 -->
							<button v-if="item.if_cancel" v-on:click="cancel(item)">取消订单</button>
						<!-- 退款取消订单 -->
							<button v-if="item.if_refund_cancel" v-on:click="refund(item.order_id)">退款</button>
						<!-- 物流跟踪 -->
							<button v-if="item.if_deliver" v-on:click="deliver(item.order_id)">物流跟踪</button>
						<!-- 收货 -->
							<button v-if="item.if_receive" v-on:click="receive(item.order_id)">收货</button>
						<!-- 评价 -->
							<button v-if="item.if_evaluation" v-on:click="evaluate(item.order_id)">评价</button>
						</div>

					</div>
				</div>
				<!-- 付款 -->
				<mt-button v-if="order_group.pay_amount && order_group.pay_amount>0" type="primary" @click='pay(order_group.pay_sn)' class="ds-button-large mt-5 mb-5">付款</mt-button>
			</div>
		</div>
		<empty-record v-else-if="order_group_list && !order_group_list.length"></empty-record>
	</div>
	</div>
</template>

<script>
import { getOrderList, cancelOrder, receiveOrder } from '../../../api/memberOrder'
import { Toast, MessageBox, Indicator } from 'mint-ui'
import { mapState } from 'vuex'
import EmptyRecord from '../../../components/EmptyRecord'
export default {
  components: {
    EmptyRecord
  },
  name: 'MemberOrderList',
  data () {
    return {
      order_id: 0,

      orderNav: [
        {
          'name': '全部',
          'id': ''
        },
        {
          'name': '待付款',
          'id': 'state_new'
        },
        {
          'name': '待发货',
          'id': 'state_pay'
        },
        {
          'name': '待收货',
          'id': 'state_send'
        },
        {
          'name': '待评价',
          'id': 'state_noeval'
        }
      ],
      stateType: this.$route.query.state ? this.$route.query.state : '',
      orderDetailVisible: false,
      wrapperHeight: 0,
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      order_group_list: false

    }
  },

  mounted () {
    this.wrapperHeight = document.documentElement.clientHeight - 140
  },
  computed: {
    ...mapState({
      user: state => state.member.info
    })
  },
  created: function () {
    // this.getOrderList(true)
  },

  methods: {
    setOrderNavActive (index) {
      this.stateType = index
      this.getOrderList(true)
    },
    showMessage (message, title) {
      MessageBox.alert(message, title)
    },
    cancel (orderInfo) {
      MessageBox.confirm('确定要取消该订单吗？').then(action => {
        cancelOrder(orderInfo.order_id).then(res => {
          this.getOrderList(true)
        }).catch(function (error) {
          Toast(error.message)
        })
      })
    },
    receive (orderId) {
      MessageBox.confirm('确定该订单已收货吗？').then(action => {
        receiveOrder(orderId).then(res => {
          this.getOrderList(true)
        }).catch(function (error) {
          Toast(error.message)
        })
      })
    },
    deliver (orderId) {
      this.$router.push({ name: 'MemberOrderDeliver', query: { order_id: orderId } })
    },
    refund (orderId) {
      this.$router.push({ name: 'MemberRefundForm', query: { order_id: orderId } })
    },
    evaluate (orderId) {
      this.$router.push({ name: 'MemberOrderEvaluate', params: { order_id: orderId } })
    },
    pay (paySn) {
      this.$router.push({ name: 'MemberBuyPay', query: { pay_sn: paySn, pay_type: 'pay_new' } })
    },
    getOrderInfo (orderId) {
      this.orderDetailVisible = true
      this.order_id = orderId
    },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getOrderList(false)
      }
    },
    getOrderList (ifReplace) {
      Indicator.open()
      if (ifReplace) {
        this.loading = false
        this.params.page = 1
        this.isMore = true
      }

      getOrderList(this.params, this.stateType, '').then(res => {
        Indicator.close()
        if (res.result.hasmore) {
          this.isMore = true
        } else {
          this.isMore = false
        }

        let tOrderGroup = res.result.order_group_list

        if (tOrderGroup) {
          if (ifReplace || !this.order_group_list) {
            this.order_group_list = tOrderGroup
          } else {
            this.order_group_list = this.order_group_list.concat(tOrderGroup)
          }
        }
      }).catch(function (error) {
        Indicator.close()
        Toast(error.message)
      })
    }
  }
}
</script>
<style  lang="scss" scoped>
.member-order-list {
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
	.order-body {
		position:relative;padding-top:2.5rem;
		.list {
			width: 100%;
			margin-top:0.5rem;
			.tips-body {
				height: 2.2rem;
				background: rgba(255, 255, 255, 1);
				box-shadow: 0 0.5px 0 0 rgba(232, 234, 237, 1);
				display: flex;
				justify-content: space-between;
				padding: 0 0.75rem 0 0.5rem;
				.tips {
					font-size:0.7rem;
					color: #333;
					line-height: 2.2rem;
				}
				.statusTips {
					color: #666666;
				}
				img {
					width: 3.8rem;
					height: 3rem;
				}
			}
			.order-image {
				height: 4.55rem;
				background: rgba(250, 250, 250, 1);
				width: 100%;
				overflow: auto;
				white-space: nowrap;
				img {
					width: 3rem;
					height: 3rem;
					border-radius: 1px;
					margin: 0.85rem 0.5rem 0.5rem;
				}
			}
			.price {
				font-size: 0.7rem;
				color: rgba(78, 84, 93, 1);
				line-height: 2.2rem;
				height: 2.2rem;
				background-color: #fff;
				padding: 0 0.75rem 0 0;
				border-bottom: 1px solid #e8eaed;
				box-sizing: border-box;
				text-align: right;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
				i {
					font-size: 0.8rem;
					color:#333;
					padding-left:0.15rem;
					font-style: normal;
					&.freight {
						color: #333;
						font-size: 0.6rem;
					}
				}
			}
			.btn {
				height: 2.2rem;
				display: flex;
				justify-content: flex-end;
				background: rgba(255, 255, 255, 1);
				border-radius: 0.1rem;
				button {
					width: 4.5rem;
					height: 1.5rem;
					font-size: 0.7rem;
					border-radius: 0.1rem;
					margin: 0.35rem 0.75rem 0.35rem 0;
					background-color: #fff;
					border: 1px solid #ccc;
				}
				.buttonright {
					background: rgba(255, 255, 255, 1);
					border-radius: 0.1rem;
					color: $primaryColor;
					border: 1px solid #e93b3d;
				}
			}
		}
		.loading-wrapper {
			text-align: center;
			p {
				color: #c3c3c3;
				font-size: 0.6rem;
				font-weight: 'Regular';
				margin: 0.5rem auto;
			}
		}
	}
	.mint-popup {
		width: 100%;
		height: 11.75rem;
	}
}
</style>
