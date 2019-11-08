<!-- List.vue -->
<template>
	<div class="ui-review-list">
		<div class="list" v-for="(item, index) in list" v-bind:key="index">
			<div>
				<span>
					{{ utils.replaceStr(item.author.username) }}

				</span>
				<span>{{ getTime(item.point_addtime) }}</span>
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
		div {
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
