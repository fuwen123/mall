<!-- 商品详情  -->
<template>
	<div
		class="ui-detail-swiper"
		v-bind:class="{
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
			index: state => state.pointsgoods.index,
		})
	},


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
	&.has-goods-stock {
		bottom: 4.1rem;
	}

}
</style>
