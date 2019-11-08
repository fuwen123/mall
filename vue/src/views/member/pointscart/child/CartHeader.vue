<template>
	<div class="common-header-wrap">
		<mt-header class="common-header" title="购物车">
			<mt-button icon="back" slot="left" @click="$router.go(-1)"></mt-button>
			<mt-button slot="right" @click="changeFinishStatus()" v-if="!isFinish && !isEmpty">编辑</mt-button>
			<mt-button slot="right" @click="changeFinishStatus()" v-if="isFinish">完成</mt-button>
		</mt-header>
	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
export default {
	data() {
		return {
			isFinish: false //是否是完成状态 编辑-完成false  ： 完成 - 编辑 true  s - true - false
		}
	},

	props: {
		issShowTabbar: {
			type: Number,
			default: 0
		},
		isEmpty: {
			type: Boolean,
			default: false
		}
	},

	computed: {},

	methods: {
		/*
		 * goBack: 返回上一页
		 */
		goBack() {
			this.$router.go(-1)
		},

		/*
		 *  changeFinishStatus: 点击编辑和完成向父组件发送事件， 编辑状态， 列表默认全选， 完成状态默认全部不选中, 并改变是否是完成的状态
		 */
		changeFinishStatus() {
			this.isFinish = !this.isFinish
			let data = { isFinish: this.isFinish }
			this.$parent.$emit('change-list-selected', data)
		}
	}
}
</script>

<style lang="scss" scoped>
.cart-header-wrapper {
	position: fixed;
	width: -webkit-fill-available;
	span {
		position: absolute;
		font-size:0.75rem;
		color: rgba(78, 84, 93, 1);
		display: inline-block;
		height:2.2rem;
		line-height:2.2rem;
		top: 0;
		right:0.75rem;
	}
}
</style>
