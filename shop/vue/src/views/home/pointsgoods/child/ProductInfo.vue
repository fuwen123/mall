<template>
	<!-- 商品 -->
	<div class="commodity-wrapper">
		<!-- 轮播图 -->
		<info-goods-swipe></info-goods-swipe>
		<!-- 商品信息 -->
		<info-item></info-item>

		<!-- 购买 -->
		<info-buy></info-buy>


		<!-- 推荐商品  -->
		<recommend-item></recommend-item>
		<div style="height:0.25rem; background-color: #F0F2F5"></div>
	</div>
</template>

<script>
import InfoGoodsSwipe from './InfoGoodsSwipe'
import InfoItem from './InfoItem'
import InfoBuy from './InfoBuy'
import RecommendItem from './RecommendItem'

import { mapState, mapMutations } from 'vuex'

export default {
  data () {
    return {}
  },

  created () {},

  components: {
    InfoGoodsSwipe,
    InfoItem,
    InfoBuy,
    RecommendItem
  },

  computed: mapState({
  }),

  mounted () {},

  methods: {}
}
</script>

<style lang="scss" scoped>
.commodity-wrapper {
	height: 100%;
}
</style>
