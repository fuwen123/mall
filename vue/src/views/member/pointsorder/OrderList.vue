<template>
  <div class="member-order-list">
	  <div class="common-header-wrap">
		  <mt-header title="兑换记录" class="common-header">
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
    <div class="order-body" v-if="order_list && order_list.length" >
			<div class="list" v-for="item in order_list" :key="item.point_orderid">
				<div @click="$router.push({name:'MemberPointsOrderDetail',query:{point_orderid:item.point_orderid}})">
				<div class="tips-body">
					<span class="tips"> 订单编号: {{ item.point_ordersn }} </span>
					<span class="title tips statusTips" >
						{{ item.point_orderstatetext }}
					</span>
				</div>
				<div
					class="order-image"
					v-if="item.prodlist.length > 0"
				>
					<img
						v-bind:src="image.pointog_goodsimage"
						v-for="image in item.prodlist"
					/>

				</div>
				<div class="price">
					(共计{{ item.prodlist.length }}种商品) 合计 :  <i>{{ item.point_allpoint }}积分</i
					>
				</div>
				</div>
				<div class="order-list-opratio">

					<!-- 收货 -->
					<div class="btn" v-if="item.point_orderstate =='30'">
						<button v-on:click="receive(item.point_orderid)">收货</button>
					</div>
					<!-- 取消 -->
					<div class="btn" v-if="item.point_orderstate =='20'">
						<button v-on:click="cancel(item.point_orderid)">取消</button>
					</div>

				</div>
			</div>

    </div>
    <empty-record v-else-if="order_list && !order_list.length"></empty-record>
  </div>
  </div>
</template>

<script>
import { getOrderList, cancelOrder, receiveOrder } from '../../../api/memberPointsorder'
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
          'name': '待发货',
          'id': 'state_pay'
        },
        {
          'name': '待收货',
          'id': 'state_send'
        },
        {
          'name': '已取消',
          'id': 'state_cancel'
        },
        {
          'name': '已完成',
          'id': 'state_finish'
        }
      ],
      stateType: this.$route.query.state ? this.$route.query.state : '',
      orderDetailVisible: false,
      wrapperHeight: 0,
		params: { 'page': 0, 'per_page': 10 },
		loading: false, // 是否加载更多
		isMore: true, // 是否有更多
      order_list: false,


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

  },

  methods: {
    setOrderNavActive (index) {
      this.stateType = index
      this.getOrderList(true)
    },

    cancel (orderId) {
      MessageBox.confirm('确定要取消该订单吗？').then(action => {
        cancelOrder(orderId).then(res => {
          this.$router.go(0)
        }).catch(function (error) {
          Toast(error.message)
        })
      })
    },
	  receive (orderId) {
		  MessageBox.confirm('确定该订单已收货吗？').then(action => {
			  receiveOrder(orderId).then(res => {
				  this.$router.go(0)
			  }).catch(function (error) {
				  Toast(error.message)
			  })
		  })
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

            let tOrderList = res.result.order_list

            if (tOrderList) {
              if (ifReplace || !this.order_list) {
                this.order_list = tOrderList
              } else {
                this.order_list = this.order_list.concat(tOrderList)
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
		height:2.2rem;
		width: 100%;
		top:2.2rem;
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
				font-size:0.7rem;
				color: #333;
				height: 100%;
				text-align: center;
				line-height:2.2rem;
				border-bottom:0.1rem solid transparent;
				&.active {
					color: $primaryColor;
					border-bottom-color: $primaryColor;
				}
			}
		}
	}
	.order-body {
		position:relative;top:2.5rem;
		.list {
			width: 100%;
			margin-top:0.5rem;
			.tips-body {
				height:2.2rem;
				background: rgba(255, 255, 255, 1);
				box-shadow: 0 0.5px 0 0 rgba(232, 234, 237, 1);
				display: flex;
				justify-content: space-between;
				padding: 0 0.75rem 0 0.5rem;
				.tips {
					font-size:0.7rem;
					color: #333;
					line-height:2.2rem;
				}
				.statusTips {
					color: #666666;
				}
			}
			.order-image {
				height:4.5rem;
				background: rgba(250, 250, 250, 1);
				width: 100%;
				overflow: auto;
				white-space: nowrap;
				img {
					width:3rem;
					height:3rem;
					border-radius: 1px;
					margin:0.9rem 0.5rem 0.5rem;
				}
			}
			.price {
				font-size:0.7rem;
				color: rgba(78, 84, 93, 1);
				line-height:2.2rem;
				height:2.2rem;
				background-color: #fff;
				padding: 0 0.75rem 0 0;
				border-bottom: 1px solid #e8eaed;
				box-sizing: border-box;
				text-align: right;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
				i {
					font-size:1rem;
					padding-left:0.25rem;
					font-style: normal;
					&.freight {
						color: #333;
						font-size:0.6rem;
					}
				}
			}
			.btn {
				height:2.2rem;
				display: flex;
				justify-content: flex-end;
				background: rgba(255, 255, 255, 1);
				border-radius:0.1rem;
				button {
					width:4.5rem;
					height:1.5rem;
					font-size:0.7rem;
					border-radius:0.1rem;
					margin:0.35rem 0.75rem 0.35rem 0;
					background-color: #fff;
					border: 1px solid #ccc;
				}
				.buttonright {
					background: rgba(255, 255, 255, 1);
					border-radius:0.1rem;
					color: $primaryColor;
					border: 1px solid #e93b3d;
				}
			}
		}
		.loading-wrapper {
			text-align: center;
			p {
				color: #c3c3c3;
				font-size:0.6rem;
				margin:0.5rem auto;
			}
		}
	}
	.mint-popup {
		width: 100%;
		height:12rem;
	}
}
</style>
