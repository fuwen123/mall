<template>
  <div class="container">
    <home-common-search :from='"home"'></home-common-search>
    <index-banner v-if="banners && banners.length > 0"
                  :items="banners"></index-banner>
    <index-menu :items="navs"></index-menu>
    <index-article class="mb-5"
                   :items="articles"></index-article>
    <index-floor-ads v-if="floorAds"
                     :ad="floorAds[0]"></index-floor-ads>
    <index-three-ads :items="promotionAds"></index-three-ads>
    <index-product-list :items="goodProducts"
                        title="热门推荐"
                        :type="popular"
                        v-if="goodProducts && goodProducts.length > 0"></index-product-list>
    <index-product-list :items="hotProducts"
                        title="销量排行"
                        :type="sale"
                        v-if="hotProducts && hotProducts.length > 0"></index-product-list>
    <index-floor-ads v-if="floorAds"
                     :ad="floorAds[1]"></index-floor-ads>
    <index-product-list :items="recentlyProducts"
                        title="新品上架"
                        :type="recently"
                        v-if="recentlyProducts && recentlyProducts.length > 0"></index-product-list>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { Toast } from 'mint-ui'
import IndexProductList from './IndexProductList'
import HomeCommonSearch from '../common/HomeCommonSearch'
import IndexBanner from './IndexBanner'
import IndexMenu from './IndexMenu'
import IndexFloorAds from './IndexFloorAds'
import IndexThreeAds from './IndexThreeAds'
import IndexArticle from './IndexArticle'
export default {
  name: 'HomeIndex',
  data () {
    return {
      isshowBacktop: true,
      popular: '',
      sale: 'goods_salenum',
      recently: 'goods_addtime'
    }
  },
  components: {
    IndexProductList,
    HomeCommonSearch,
    IndexBanner,
    IndexMenu,
    IndexFloorAds,
    IndexThreeAds,
    IndexArticle
  },
  mounted () {
  },
  created: function () {
    this.fetchHomeAd({}).then(
      response => {
      },
      error => {
        Toast(error.message)
      }
    )
    this.fetchHomeArticle({}).then(
      response => {
      },
      error => {
        Toast(error.message)
      }
    )
    this.fetchHomeProduct({}).then(
      response => {
      },
      error => {
        Toast(error.message)
      }
    )
    this.fetchConfig({}).then(
      response => {
      },
      error => {
        Toast(error.message)
      }
    )
  },
  computed: {
    ...mapState({
      config: state => state.config.config,
      banners: state => state.home.banners,
      navs: state => state.home.navs,
      floorAds: state => state.home.floorAds,
      promotionAds: state => state.home.promotionAds,
      articles: state => state.home.articles,
      hotProducts: state => state.home.hotProducts,
      recentlyProducts: state => state.home.recentlyProducts,
      goodProducts: state => state.home.goodProducts
    })
  },
  methods: {
    ...mapActions({
      fetchHomeAd: 'fetchHomeAd',
      fetchHomeArticle: 'fetchHomeArticle',
      fetchHomeProduct: 'fetchHomeProduct',
      fetchConfig: 'fetchConfig'
    })
  }
}
</script>

<style scoped>
</style>
