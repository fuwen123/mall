<template>
  <mt-swipe v-bind:style="getBannerStyle" :showIndicators="isShowIndicators">
    <mt-swipe-item v-for="(item, index) in items" :key="index">
      <img :style="getBannerStyle" v-lazy="item.adv_code" v-if='item.adv_code' @click="goAdUrl(item)">
      <img :style="getBannerStyle" src="../../../assets/image/home/default_image_banner.png" v-else>
    </mt-swipe-item>
  </mt-swipe>
</template>

<script>
export default {
  name: 'IndexBanner',
  data () {
    return {
    }
  },
  props: ['items'],
  computed: {
    getBannerStyle: function () {
      const { width } = window.screen
      let itemWidth = width
      let itemHeight = width * (94.0 / 188.0)
      return {
        width: itemWidth + 'px',
        height: itemHeight + 'px'
      }
    },
    isShowIndicators () {
      if (this.items && this.items.length > 1) {
        return true
      }
      return false
    }
  },
  methods: {
    goAdUrl (item) {
      switch (item.adv_type) {
        case 'goods':
          this.$router.push({ name: 'HomeGoodsdetail', query: { goods_id: item.adv_typedate } })
          break
        case 'store':
          this.$router.push({ name: 'HomeStoredetail', query: { id: item.adv_typedate } })
          break
        case 'article':
          this.$router.push({ name: 'HomeArticledetail', query: { article_id: item.adv_typedate } })
          break
        case 'url':
          window.location.href=item.adv_typedate
          break  
      }
    }
  }
}
</script>

<style scoped>

</style>
