<template>
    <div>
        <div class="common-search">
            <input type="text" placeholder="搜索店铺商品" readonly="true" autocomplete="off" v-model="params.keyword" @click="$router.push({name:'HomeStoreGoodsclass',query:{id:store_id}})">
        </div>
        <goodslist-body></goodslist-body>
        <common-store-footer :store_id="store_id"></common-store-footer>
    </div>
</template>

<script>
import CommonStoreFooter from '../common/CommonStoreFooter'
import { mapState, mapMutations } from 'vuex'
import GoodslistBody from './GoodslistBody'
export default {
  name: 'StoreGoodslist',
  components: {
    CommonStoreFooter,
    GoodslistBody
  },
  data () {
    return {
      store_id: this.$route.query.id ? this.$route.query.id : '',
      params: {
        keyword: this.$route.query.keyword ? this.$route.query.keyword : ''
      }

    }
  },
  created () {

  }
}
</script>

<style scoped>
    .common-search{
        padding:0.4rem 0.6rem;
    }
    .common-search input {
        box-sizing: border-box;
        width: 100%;
        height: 1.6rem;
        border-radius:0.2rem;
        background: #EDEDED url(../../../assets/image/home/icon_search.png) no-repeat 0.6rem center;
        background-size:0.55rem;
        font-size:0.7rem;
        color: #999;
        padding-left:1.65rem;
        border: 0;
    }
</style>
