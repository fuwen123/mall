<!-- List.vue -->
<template>
	<div class="ui-review-list">
		<div class="list" v-for="(item, index) in list">
			<div class="content">
				<span>
					{{ utils.replaceStr(item.author.username) }}
					<span
						v-bind:class="{
							'good-review': item.grade == 3,
							'medium-review': item.grade == 2,
							'bad-review': item.grade == 1
						}"
						>{{ getGrade(item.grade) }}</span
					>
				</span>
				<span>{{ getTime(item.created_at) }}</span>
			</div>
			<p v-if="item.content">{{ item.content }}</p>
			<p v-if="!item.content">无评价信息</p>
      <div class="explain" v-if="item.geval_explain">
					<div class="title">掌柜回复</div>
					<div class="mt-5">{{item.geval_explain}}</div>
				</div>
		</div>
	</div>
</template>

<script>
export default {
	data() {
		return {}
	},

	props: ['list'],

	created() {},

	methods: {
		/*
			getGrade: 获取评论的等级
			@params： grade 等级
		 */
		getGrade(grade) {
			if (grade == 1) {
				return '差评'
			} else if (grade == 2) {
				return '中评'
			} else {
				return '好评'
			}
		},

		/*
			getTime: 获取评论的时间
			@params: timestamps 时间戳
		 */
		getTime(timestamps) {
			let date = new Date(timestamps * 1000)
			let year = date.getFullYear(),
				month = date.getMonth() + 1,
				day = date.getDate()
			return year + '-' + month + '-' + day
		}
	}
}
</script>

<style lang="scss">
.ui-review-list {
	background: rgba(255, 255, 255, 1);
	.list {
		padding: 0.75rem;
		border-bottom: 0.5px solid #e8eaed;
		color: #333;
		font-size: 0.75rem;
    .explain{padding:.5rem;background: #eee;margin-top:.5rem;
				.title{color:#333}
			}
		.content {
			overflow: hidden;
			padding-bottom: 0.75rem;
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
						margin-left: 0.75rem;
						color: #ffffff;
						font-size: 0.6rem;
					}
				}
				&:last-child {
					color: #8f8e94;
					font-size: 0.6rem;
				}
				&.good-review {
					background: #fc2e39;
					width: 1.8rem;
					height: 0.8rem;
					text-align: center;
					background-size: cover;
					line-height: 0.8rem;
					border-radius: 0.4rem;
				}
				&.medium-review {
					background: #e93b3d;
					width: 1.8rem;
					height: 0.8rem;
					text-align: center;
					background-size: cover;
					line-height: 0.8rem;
					border-radius: 0.4rem;
				}
				&.bad-review {
					background: #c3c3c3;
					width: 1.8rem;
					height: 0.8rem;
					text-align: center;
					background-size: cover;
					line-height: 0.8rem;
					border-radius: 0.4rem;
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
}
</style>
