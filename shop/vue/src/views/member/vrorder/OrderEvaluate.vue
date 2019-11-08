<template>
	<div class="container">
		<!-- header -->
		<div class="common-header-wrap">
			<mt-header title="评价订单" class="common-header">
				<mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
			</mt-header>
		</div>

		<!-- body -->
		<div class="body">
			<div
				class="order-comment-body"
			>
				<div class="body-list">
					<div class="image">
						<img
							v-bind:src="orderItem.goods_image_url"
						/>

					</div>
					<div class="comment">
						<span>{{ orderItem.goods_name }}</span>
						<span class="common-score-wrapper" @click="setStar(orderItem.goods_id,index,$event)" ref="star"><i class="iconfont front" :style="{width:score/5*100+'%'}"></i><i class="iconfont back"></i></span>
					</div>
				</div>
				<div class="enter">
					<textarea
						placeholder="请在此输入评价"
						v-model="result.goods[orderItem.goods_id].comment"
					></textarea>
				</div>
			</div>
		</div>
		<mt-button type="primary" @click='submit' class="ds-button-large">提交</mt-button>
	</div>
</template>

<script>
import { Toast } from 'mint-ui'
import { getOrderEvaluateInfo, saveOrderEvaluate } from '../../../api/memberVrOrder'

export default {
  data () {
    return {
      orderItem: {},
      result: { goods: {} },
      score: 5
    }
  },
  created () {
    if (this.$route.params.order_id) {
      getOrderEvaluateInfo(this.$route.params.order_id).then(res => {
        this.orderItem = res.result.order_info
          this.result.goods[this.orderItem.goods_id] = { comment: '', score: 5 }
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  },
  methods: {
    submit () {
      saveOrderEvaluate(this.$route.params.order_id, this.result).then(res => {
        this.$router.go(-1)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    setStar (goods_id, index, event) {
      let starWidth = this.$refs.star[0].offsetWidth
      let score = Math.ceil(event.offsetX / starWidth * 5)
      this.result.goods[goods_id].score = score
      this.score=score
    }
  }
}
</script>

<style lang="scss" scoped>
	.common-score-wrapper .back{display: block}
.container {
	display: flex;
	flex-direction: column;
	justify-content: flex-start;
	align-items: stretch;

	.body {
		top:2.2rem;
		width: 100%;
		.order-comment-body {
			background: rgba(255, 255, 255, 1);
			padding:0.75rem;
			.body-list {
				display: flex;
				justify-content: left;
				align-content: center;
				align-items: center;
				padding-bottom:0.75rem;
				border-bottom: 1px solid #e8eaed;
			}
			.image {
				width:2.7rem;
				height:2.7rem;
				flex-shrink: 0;
				img {
					width: 100%;
					height: 100%;
				}
			}
			.comment {
				flex-basis: 100%;
				padding-left:0.75rem;
				span {
					font-size:0.8rem;
					color: #7c7f88;
					text-align: left;
					display: block;
				}
				ul {
					display: flex;
					justify-content: space-between;
					align-content: center;
					align-items: center;
					margin-top: 1.2rem;
					li {
						img {
							width:1rem;
							height:1rem;
							flex-shrink: 0;
						}
						label {
							font-size:0.7rem;
							color: rgba(78, 84, 93, 1);
							font-weight: normal;
						}
					}
				}
			}
			.enter {
				padding-top:0.75rem;
				textarea {
					width: 100%;
					height:6rem;
					background: rgba(247, 249, 250, 1);
					border: 1px solid #f7f9fa;
					box-sizing: border-box;
					padding:0.5rem 0 0 0.5rem;
					font-size:0.7rem;
					-webkit-appearance: none;
					outline: none;
				}
			}
		}
	}
}
</style>
