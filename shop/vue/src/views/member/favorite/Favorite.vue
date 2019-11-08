<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header class="common-header" title="我的收藏">
                <mt-button icon="back" slot="left" @click="goBack"></mt-button>
            </mt-header>
        </div>
            <mt-navbar class="tab-nav" v-model="selected">
                <mt-tab-item id="1">搜藏店铺</mt-tab-item>
                <mt-tab-item id="2">搜藏商品</mt-tab-item>
            </mt-navbar>
             <favorite-store v-bind:class="[(selected==1)?'active':'','tab-containt']"></favorite-store>
             <favorite-goods v-bind:class="[(selected==2)?'active':'','tab-containt']"></favorite-goods>
    </div>
</template>
<script>
import FavoriteGoods from './FavoriteGoods'
import FavoriteStore from './FavoriteStore'
export default {
  components: {
    FavoriteGoods,
    FavoriteStore
  },
  name: 'MemberFavorite',
  data () {
    return {
      selected: '1'
    }
  },
  methods: {
    goBack () {
      this.$router.go(-1)
    }
  }
}
</script>
<style scoped>
    .container .tab-nav{margin-bottom:.5rem}
    .container .tab-containt{height: 0;overflow: hidden}
    .container .tab-containt.active{height: auto}
</style>
