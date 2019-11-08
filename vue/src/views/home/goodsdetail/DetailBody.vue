<!-- 商品详情  -->
<template>
	<div
		class="ui-detail-swiper"
		v-bind:class="{
			'hide-promotion-bottom': ispromotion,
			'has-goods-stock': isStock <= 0
		}"
	>
		<product-info v-if="index == 0"></product-info>
		<product-desc v-if="index == 1"></product-desc>
		<product-review v-if="index == 2"></product-review>
	</div>
</template>

<script>
import ProductInfo from './child/ProductInfo'
import ProductDesc from './child/ProductDesc'
import ProductReview from './child/ProductReview'
import { mapState, mapMutations } from 'vuex'
export default {
	data() {
		return {}
	},

	props: ['isStock'],

	components: {
		ProductInfo,
		ProductDesc,
		ProductReview
	},

	computed: {
		...mapState({
			index: state => state.goodsdetail.index,
			ispromotion: state => state.goodsdetail.ispromotion
		})
	},

	watch: {},

	mounted() {},

	created() {},

	methods: {
		...mapMutations({})
	}
}
</script>

<style lang="scss" scoped>
.ui-detail-swiper {
	position: absolute;
	width: 100%;
	top: 2.25rem;
	bottom: 2.5rem;
	left: 0;
	right: 0;
	overflow: auto;
	z-index: 0;
	/* border: 1px solid red; */
	background: rgba(240, 242, 245, 1);

	&.hide-promotion-bottom {
		bottom: 0;
	}
}
</style>
