<!-- 商品详情 -->
<template>
    <div class="product-detail-wrapper" v-if="productDetail">
        <!-- header  -->
        <detail-header v-if="!isPreviewPicture"></detail-header>
        <!-- body -->
        <detail-body :isStock="productDetail.goods_storage"></detail-body>
        <!-- footer -->
        <detail-footer v-if="!isPreviewPicture"></detail-footer>
        <!-- 预览图片 -->
        <preview-picture
                v-if="isPreviewPicture"
                :defaultindex="swipeId"
                :isshow="isPreviewPicture"
        ></preview-picture>
        <!-- 促销信息 -->
        <promotion-popup
                v-if="promoPopstatus"
                :promo-popstatus="promoPopstatus"
        ></promotion-popup>
    </div>
</template>

<script>
import { Toast } from 'mint-ui'
import DetailHeader from './DetailHeader'
import DetailBody from './DetailBody'
import DetailFooter from './DetailFooter'
import PreviewPicture from './child/PreviewPicture'
import PromotionPopup from './child/PromotionPopup'

// import { scoreGet } from '../../api/score'
import { mapState, mapMutations, mapActions } from 'vuex'

export default {
  data () {
    return {
      cartNumber: 0,
      productId: this.$route.query.goods_id ? this.$route.query.goods_id : '',
      hideFooter: false,
      popupVisible: true,
      currentScore: 0,
      bargain_id: this.$route.query.bargain_id
    }
  },

  components: {
    DetailHeader,
    DetailBody,
    DetailFooter,
    PreviewPicture,
    PromotionPopup
  },

  created () {
    let extra = {}
    if (this.bargain_id) {
      extra['bargain_id'] = this.bargain_id
    }
    this.getGoodsDetail({ goods_id: this.productId, token: this.token, extra: extra }).catch(error => {
      Toast(error.message)
    })
    this.saveCartState(false)
  },

  computed: mapState({
    productDetail: state => state.goodsdetail.detailInfo,
    currentProductId: state => state.goodsdetail.currentProductId,
    token: state => state.member.token,
    isPreviewPicture: state => state.goodsdetail.isPreviewPicture,
    swipeId: state => state.goodsdetail.swipeId,
    promoPopstatus: state => state.goodsdetail.promoPopstatus,
    config: state => state.config.config
  }),

  mounted () {
    this.$nextTick(() => {})
  },

  beforeRouteUpdate (to, from, next) {
    next()
    window.location.reload()
  },

  methods: {
    ...mapMutations({
      saveCartState: 'saveCartState'
    }),
    ...mapActions({
      fetchConfig: 'fetchConfig',
      getGoodsDetail: 'getGoodsDetail'
    }),
    loadConfig (imgUrl, desc, link) {
      this.fetchConfig().then(
        response => {
          let wechat = response.config['wechat.web']
          // let openid = this.$cookie.get('o')
          // 微信已授权
          if (wechat) {
            this.setWechatConfig(wechat, imgUrl, desc, link)
          }
        },
        error => {}
      )
    },
    setWechatConfig (config, imgUrl, desc, link) {
      this.wxApi.wxRegister(config, '商品详情', imgUrl, desc, link)
    }
  }
}
</script>

<style lang="scss" scoped>
    .product-detail-wrapper {
        height: 100%;
        width: auto;
    }
</style>
