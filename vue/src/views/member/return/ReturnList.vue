<template>
	<div class="member-order-list">
		<div class="common-header-wrap">
			<mt-header title="退货列表" class="common-header">
				<mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
			</mt-header>
		</div>
		<div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
			<div class="order-body" v-if="return_list && return_list.length">
				<div class="list" v-for="(item, index) in return_list" v-bind:key="item.refund_id">
					<div @click="$router.push({name:'MemberReturnView',query:{refund_id:item.refund_id}})">
						<div class="tips-body">
							<span class="tips"> 退货编号: {{ item.refund_sn }} </span>
							<span class="title tips statusTips" >{{ item.seller_state }}</span>
						</div>
						<div class="order-image">
							<img v-bind:src="item.goods_img_480"/>
						</div>
						<div class="price">退款金额 : ￥ <i>{{ item.refund_amount }}</i></div>
					</div>
					<div class="order-list-opratio">
						<!-- 锁定-->
						<div class="btn">
							<button v-if="item.ship_state == 1" v-on:click="$router.push({name:'MemberReturnSend',query:{refund_id:item.refund_id}})">退货</button>
							<button v-if="item.delay_state == 1" v-on:click="delay(item.refund_id)">延迟</button>
						</div>
					</div>
				</div>
			</div>
			<empty-record v-else-if="return_list && !return_list.length"></empty-record>
		</div>
	</div>
</template>

<script>
import { getReturnList, delayReturn } from '../../../api/memberReturn'
import { Toast, Indicator } from 'mint-ui'

import EmptyRecord from '../../../components/EmptyRecord'
export default {
  components: {
    EmptyRecord
  },
  name: 'MemberReturnList',
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      order_id: 0,
      stateType: '',
      orderDetailVisible: false,
      wrapperHeight: 0,
      return_list: false
    }
  },

  mounted () {
    this.wrapperHeight = document.documentElement.clientHeight - 90
  },
  computed: {

  },
  created: function () {

  },
  methods: {
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getReturnList(false)
      }
    },
    delay (refund_id) {
      delayReturn(refund_id).then(res => {
        this.$router.go(0)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    getReturnList (ifReplace) {
      Indicator.open()
      if (ifReplace) {
        this.loading = false
        this.params.page = 1
        this.isMore = true
      }
      getReturnList(this.params).then(res => {
        Indicator.close()
        if (res.result.hasmore) {
          this.isMore = true
        } else {
          this.isMore = false
        }
        let tReturnGroup = res.result.return_list
        if (tReturnGroup) {
          if (ifReplace || !this.return_list) {
            this.return_list = tReturnGroup
          } else {
            this.return_list = this.return_list.concat(tReturnGroup)
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
	.order-body {
		.list {
			width: 100%;
			margin-top:0.5rem;
			.tips-body {
				height:2rem;
				background: rgba(255, 255, 255, 1);
				box-shadow: 0 0.1rem 0 0 rgba(232, 234, 237, 1);
				display: flex;
				justify-content: space-between;
				padding: 0 0.8rem 0 0.5rem;
				.tips {
					font-size:0.7rem;
					color: #333;
					line-height:2rem;
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
					border-radius:0.1rem;
					margin:0.8rem 0.5rem 0.5rem;
				}
			}
			.price {
				font-size:0.7rem;
				color: rgba(78, 84, 93, 1);
				line-height:2rem;
				height:2rem;
				background-color: #fff;
				padding: 0 0.8rem 0 0;
				border-bottom: 1px solid #e8eaed;
				box-sizing: border-box;
				text-align: right;
				overflow: hidden;
				text-overflow: ellipsis;
				white-space: nowrap;
				i {
					font-size:1rem;
					padding-left:0.4rem;
					font-style: normal;
					&.freight {
						color: #333;
						font-size:0.6rem;
					}
				}
			}
			.btn {
				height:2rem;
				display: flex;
				justify-content: flex-end;
				background: rgba(255, 255, 255, 1);
				border-radius:0.1rem;
				button {
					width:4.5rem;
					height:1.5rem;
					font-size:0.7rem;
					border-radius:0.1rem;
					margin:0.4rem 0.8rem 0.4rem 0;
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
