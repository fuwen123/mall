<template>
  <div class="category">
    <div class="product-list-header">
      <div class="slot iconfont" @click="$router.go(-1)">&#xe64f;</div>
      <div class="common-search">
        <input type="text" placeholder="请输入关键词" autocomplete="off" v-model="keyword">
      </div>
      <mt-button class="buttom" size="small" type="default" @click="$router.go(-1)" v-if="ifKeywords">取消</mt-button>
      <mt-button class="buttom" size="small" type="primary" @click="search" v-else>搜索</mt-button>
    </div>
    <goodsclass-body></goodsclass-body>
  </div>
</template>

<script>

import GoodsclassBody from './GoodsclassBody'
export default {
  name: 'HomeGoodsclass',
  data () {
    return {
      keyword: ''
    }
  },
  components: {
    GoodsclassBody
  },
  computed: {
    ifKeywords () {
      if (this.keywords === '') {
        return true
      } else {
        return false
      }
    }
  },
  methods: {
    search () {
      this.$router.push({ 'name': 'HomeStoreGoodslist', 'query': { id: this.$route.query.id, keyword: this.keyword } })
    }
  }
}
</script>

<style scoped lang='scss'>
  .product-list-header {
    display: flex;
    height:2rem;
    padding: 0 0.25rem;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f5f5f5;
    background: #fff;
.slot{width:1.6rem;font-size:.8rem}
  }
  .common-search{
    flex:1;
    padding:0.4rem 0.6rem 0.4rem 0;
  }
  .common-search input {
    box-sizing: border-box;
    width: 100%;
    height:1.6rem;
    line-height:1.6rem;
    border-radius:0.2rem;
    background: #EDEDED ;
    background-size:0.55rem;
    font-size:0.7rem;
    color: #999;
    border: 0;
    padding-left:.5rem;
  }
  .buttom{width:2.6rem;height: 1.6rem;}
</style>
