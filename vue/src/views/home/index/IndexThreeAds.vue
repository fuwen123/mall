<template>
<div class="three-ads" v-if="items && items.length">

    <div class="ad-1" v-if="items[0]">
        <img v-lazy="items[0].adv_code" @click="goAdUrl(items[0])"/>
    </div>
    <div class="fr">
        <div class="ad-2" v-if="items[1]">
            <img v-lazy="items[1].adv_code" @click="goAdUrl(items[1])"/>
        </div>
        <div class="ad-3" v-if="items[2]">
            <img v-lazy="items[2].adv_code" @click="goAdUrl(items[2])"/>
        </div>
    </div>
</div>
</template>

<script>
export default {
  name: 'IndexThreeAds',
  data () {
    return {
    }
  },
  props: ['items'],
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
.three-ads{display: flex;}
.three-ads img{width:100%;}
</style>
