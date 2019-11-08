<template>
<div class="floor-ad" v-if="ad">
<img v-lazy="ad.adv_code" @click="goAdUrl(ad)"/>
</div>
</template>

<script>
export default {
  name: 'IndexFloorAds',
  data () {
    return {
    }
  },
  props: ['ad'],
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
.floor-ad{}
    .floor-ad img{width:100%;}
</style>
