<!-- Evaluation.vue -->
<template>
	<div class="ui-evaluation">
		<div class="ui-evaluation-header">
			<div class="flex-header">
				<div
					v-for="(item, index) in staticData"
					v-bind:key="item.id"
					v-bind:class="{ active: currentTag == item.value }"
					v-on:click="changeTab(item.value, item.grade)"
				>
					{{ item.name }}
				</div>
			</div>
		</div>
		<div
			class="ui-evaluation-body"
			v-infinite-scroll="loadMore"
			infinite-scroll-disabled="loading"
			infinite-scroll-distance="10"
		>
			<div
				class="list"
				v-for="(item, index) in reviewList"
				v-if="reviewList.length > 0"
			>
				<div>
					<span>
						{{ item.geval_isanonymous?'匿名':item.geval_frommembername }}
						<span
							v-bind:class="{
								'good-review': item.geval_scores > 3,
								'medium-review': item.geval_scores > 1,
								'bad-review': item.geval_scores == 1
							}"
							>{{ getGrade(item.geval_scores) }}</span
						>
					</span>

					<span>{{ getTime(item.geval_addtime) }}</span>
				</div>
				<p v-if="item.geval_content">{{ item.geval_content }}</p>
				<p v-if="!item.geval_content">无评价信息</p>
			</div>

			<div class="list-empty" v-if="reviewList.length <= 0">
				<p>本商品暂无评价</p>
			</div>
		</div>
	</div>
</template>

<script>
import { evaluation } from '../static'
import { getReviewList } from '../../../../api/homegoodsdetail'
export default {
  data () {
    return {
      staticData: evaluation,
      id: this.$store.state.goodsdetail.currentProductId
        ? this.$store.state.goodsdetail.currentProductId
        : '',
      currentTag: 'total',
      grade: 0,
      subTotal: {},
      reviewList: [],
      page: 0,
      loading: false,
      total: 1
    }
  },
  created () {
  },
  methods: {

    loadMore () {
      this.loading = true
      this.page = ++this.page
      if (this.page <= this.total) {
        this.loading = false
        this.getReviewList(true)
      }
    },
    getReviewList (ispush) {
      getReviewList(this.id, this.grade, this.page).then(res => {
        if (res) {
          if (ispush) {
            this.reviewList = this.reviewList.concat(res.result.goods_eval_list)
          } else {
            this.reviewList = res.result.goods_eval_list
          }
          if (res.hasmore) {
            this.loading = false
          } else {
            this.loading = true
          }
          this.total = res.page_total
        }
      })
    },
    changeTab (value, grade) {
      this.currentTag = value
      this.grade = grade
      this.getReviewList(false)
    },
    getGrade (grade) {
      if (grade == 1) {
        return '差评'
      } else if (grade > 3) {
        return '好评'
      } else {
        return '中评'
      }
    },
    getTime (timestamps) {
      let date = new Date(timestamps * 1000)
      let year = date.getFullYear()
      let month = date.getMonth() + 1
      let day = date.getDate()
      return year + '-' + month + '-' + day
    }
  }
}
</script>

<style lang="scss" scoped>
.ui-evaluation {
	.ui-evaluation-header {
		background: #ffffff;
		.flex-header {
			width: auto;
			display: flex;
			display: -webkit-flex;
			display: -moz-flex;
			flex-basis: 100%;
			justify-content: space-around;
			align-content: center;
			align-items: center;
			height:2.2rem;
			div {
				color: #5c5958;
				font-size:0.7rem;
				height:1.2rem;
				border: 0.5px solid #333;
				padding: 0 0.45rem;
				line-height: 1.2rem;
				text-align: center;
				border-radius: 0.2rem;
				&.active {
					color: #ffffff;
					background: $primaryColor;
					border: 0.5px solid transparent;
				}
			}
		}
	}
	.ui-evaluation-body {
		padding: 0 0.75rem;
		background: rgba(255, 255, 255, 1);
		.list {
			padding: 0.75rem 0;
			border-bottom: 0.5px solid rgba(232, 234, 237, 1);
			color: #333;
			font-size:0.75rem;
			div {
				overflow: hidden;
				padding-bottom:0.75rem;
				display: flex;
				justify-content: space-between;
				align-content: center;
				align-items: center;
				span {
					&:first-child {
						display: flex;
						justify-content: space-around;
						align-content: center;
						align-items: center;
						span {
							margin-left:0.75rem;
							color: #ffffff;
							font-size:0.6rem;
						}
					}
					&:last-child {
						color: #8f8e94;
						font-size:0.6rem;
					}
					&.good-review {
						background: #fc2e39;
						width:1.8rem;
						height:0.8rem;
						text-align: center;
						background-size: cover;
						line-height:0.8rem;
						border-radius:0.4rem;
					}
					&.medium-review {
						background: #e93b3d;
						width:1.8rem;
						height:0.8rem;
						text-align: center;
						background-size: cover;
						line-height:0.8rem;
						border-radius:0.4rem;
					}
					&.bad-review {
						background: #c3c3c3;
						width:1.8rem;
						height:0.8rem;
						text-align: center;
						background-size: cover;
						line-height:0.8rem;
						border-radius:0.4rem;
					}
				}
			}
			p {
				padding: 0;
				margin: 0;
				flex-basis: 100%;
				display: -webkit-box;
				-webkit-box-orient: vertical;
				-webkit-line-clamp: 2;
				overflow: hidden;
			}
		}
		.list-empty {
			position: absolute;
			top: 50%;
			left: 50%;
			transform: translate(-50%, -50%);
			text-align: center;
			img {
				width:2.75rem;
			}
			p {
				color: #7c7f88;
				font-size:0.7rem;
				padding: 0;
				margin: 0;
				font-weight: normal;
			}
		}
	}
}
</style>
