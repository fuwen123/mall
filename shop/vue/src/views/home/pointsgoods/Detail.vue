<!-- 商品详情 -->
<template>
    <div class="product-detail-wrapper" v-if="productDetail">
        <!-- header  -->
        <detail-header v-if="!isPreviewPicture"></detail-header>
        <!-- body -->
        <detail-body :isStock="productDetail.pgoods_storage"></detail-body>
        <!-- footer -->
        <detail-footer v-if="!isPreviewPicture"></detail-footer>
        <!-- 预览图片 -->
        <preview-picture
                v-if="isPreviewPicture"
                :isshow="isPreviewPicture"
        ></preview-picture>

    </div>
</template>

<script>
import DetailHeader from './DetailHeader'
import DetailBody from './DetailBody'
import DetailFooter from './DetailFooter'
import PreviewPicture from './child/PreviewPicture'
import { getPointsgoodsInfo } from '../../../api/homePointsgoods'

import { mapState, mapMutations, mapActions } from 'vuex'

export default {
  data () {
    return {
      productId: this.$route.query.pgoods_id ? this.$route.query.pgoods_id : '',
      productDetail: {},
    }
  },

  components: {
    DetailHeader,
    DetailBody,
    DetailFooter,
    PreviewPicture
  },

  created () {
    this.getDetail()
    this.saveCartState(false)
  },

  computed: mapState({
    isPreviewPicture: state => state.pointsgoods.isPreviewPicture,
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
      saveInfo: 'saveDetailInfo',
      saveCommendList: 'saveCommendList',
      saveCartState: 'saveCartState',
      setCurrentProductId: 'setCurrentProductId'
    }),


    /*
    getDetail: 获取商品详情， 并且存入状态管理
    */
    getDetail () {
      this.setCurrentProductId(this.productId)
        getPointsgoodsInfo(this.productId).then(res => {
        if (res) {
          this.productDetail = res.result.goods_info

          this.saveInfo(res.result.goods_info)
          this.saveCommendList(res.result.goods_commend_list)
        }
      })
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
