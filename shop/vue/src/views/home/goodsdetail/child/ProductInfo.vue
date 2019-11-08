<template>
	<!-- 商品 -->
	<div class="commodity-wrapper">
		<!-- 轮播图 -->
		<info-goods-swipe></info-goods-swipe>
		<!-- 商品信息 -->
		<info-item></info-item>

		<!-- 促销商品的活动结束时间 -->
		<info-promos-time></info-promos-time>
		<!-- 促销信息 -->
		<info-promotions></info-promotions>
		<!-- 购买 -->
		<info-buy></info-buy>
		<!-- 联系客服-->
		 <info-contact></info-contact>
		<!-- 评论 -->
		<info-goods-review></info-goods-review>
		<!-- 拼团 -->
		 <info-like></info-like>

		<!-- 店铺信息 -->
		<div class="goods-detail-store" @click="$router.push({name:'HomeStoredetail',query:{id:storeInfo.store_id}})">
			<div>
				<div class="store-name"><img width="40" :src="storeInfo.store_logo">
					{{storeInfo.store_name}}

					<span class="icon-mall" v-if="storeInfo.is_platform_store">自营</span>
				</div>
				<div class="store-rate">
				<span class="equal">描述相符
					<em>{{storeInfo.store_credit.store_servicecredit.credit}}</em>
					<i>{{storeInfo.store_credit.store_servicecredit.percent_text}}</i>
				</span>
					<span class="equal">服务态度
					<em>{{storeInfo.store_credit.store_desccredit.credit}}</em>
					<i>{{storeInfo.store_credit.store_servicecredit.percent_text}}</i>
				</span>
					<span class="equal">发货速度
					<em>{{storeInfo.store_credit.store_deliverycredit.credit}}</em>
					<i>{{storeInfo.store_credit.store_servicecredit.percent_text}}</i>
				</span>
				</div>
			</div>
		</div>

		<!-- 商品搭配 -->
		<accessory-item></accessory-item>
		<!-- 推荐商品  -->
		<recommend-item></recommend-item>
		<div style="height:0.25rem; background-color: #F0F2F5"></div>
	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import InfoGoodsSwipe from './InfoGoodsSwipe'
import InfoItem from './InfoItem'
import InfoPromotions from './InfoPromotions'
import InfoPromosTime from './InfoPromosTime'
import InfoBuy from './InfoBuy'
import InfoContact from './InfoContact'
import InfoLike from './InfoLike'
import InfoGoodsReview from './InfoGoodsReview'
import RecommendItem from './RecommendItem'
import AccessoryItem from './AccessoryItem'

export default {
  data () {
    return {}
  },

  created () {},

  components: {
    InfoGoodsSwipe,
    InfoItem,
    InfoPromotions,
    InfoPromosTime,
    InfoBuy,
    InfoContact,
    InfoLike,
    InfoGoodsReview,
    RecommendItem,
    AccessoryItem
  },

  computed: {
    ...mapState({
      storeInfo: state => state.goodsdetail.storeInfo
    })
  },

  mounted () {},

  methods: {}
}
</script>

<style lang="scss" scoped>
.commodity-wrapper {
	height: 100%;
}
.goods-detail-store { display: block; position: relative; z-index: 1; padding: 0 0.5rem;background: #FFF; border-top:0.5rem solid #EEE; }
.goods-detail-store .store-name { min-height: 0.9rem; padding: 0.5rem 0 0.25rem  0; font-size: 0.7rem; line-height: 0.9rem; color: #333; }
.goods-detail-store .store-name .icon-store { display: inline-block; width: 0.8rem; height: 0.9rem; vertical-align: top; margin-right: 0.2rem; }
.goods-detail-store .store-name .icon-store:before { content: "\e7dc"; }
.goods-detail-store .store-name .icon-mall { color: #f23030;font-size: 0.55rem; margin-left: 0.5rem;}
.goods-detail-store .store-rate { height: 0.7rem; padding: 0.4rem 0 0.5rem 0; border-top: solid 0.05rem #EEE; font-size: 0; }
.goods-detail-store .store-rate span { display: inline-block; width: 33.33%; height: 0.7rem; font-size: 0.55rem; line-height: 0.7rem; color: #888; }
.goods-detail-store .store-rate span em { display: inline-block; vertical-align: middle; font-size: 0.7rem; font-weight: 600;}
.goods-detail-store .store-rate span i { display: inline-block; vertical-align: middle; width: 0.7rem; height: 0.7rem; font-size: 0.45rem; line-height: 0.7rem; font-style: normal; text-align: center; border-radius: 0.1rem;margin-left:0.2rem;}
.goods-detail-store .store-rate span.high em,
.goods-detail-store .store-rate span.equal em { color: #f23030;}
.goods-detail-store .store-rate span.high i,
.goods-detail-store .store-rate span.equal i { color: #FFF; background-color: #f23030}
.goods-detail-store .store-rate span.low em { color: #48CFAE;}
.goods-detail-store .store-rate span.low i { color: #FFF; background-color: #48CFAE;}

</style>
