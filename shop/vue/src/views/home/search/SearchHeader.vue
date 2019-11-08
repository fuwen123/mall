<template>
  <div class="product-list-header">
    <div class="slot iconfont" @click="$router.go(-1)">&#xe64f;</div>
    <div class="common-search">
      <input type="text" placeholder="请输入您要搜索的商品" autocomplete="off" v-model="keywords">
    </div>
    <mt-button class="right" size="small" type="default" @click="$router.go(-1)" v-if="ifKeywords">取消</mt-button>
    <mt-button class="right" size="small" type="primary" @click="search(keywords)" v-else>搜索</mt-button>
  </div>
</template>

<script>
import { Toast } from 'mint-ui'
import { mapMutations } from 'vuex'

export default {
  data () {
    return {
      keywords: this.keyword ? this.keyword : '',
      currenKeywords: this.utils.fetch('keyword')
    }
  },
  props: ['keyword'],
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
    ...mapMutations({
      'changeKey': 'changeKey'
    }),
    // 分类列表进入到搜索，完成后跳转到商品列表页面
    search (value) {

      if (value.replace(/\s+/g, '').length <= 0) {
        Toast('请输入您要搜索的关键字')
        return false
      } else {
        this.keywords = value
      }
      if (value) {
        this.currenKeywords.push(value)
        let data = this.utils.arrayFilter(this.currenKeywords)
        this.utils.save('keyword', data)
      }
      this.$router.push({
        name: 'HomeGoodslist',
        query: { keywords: this.keywords }
      })
    },

    // 取消返回上一级
    cancel () {
      this.clear()
      let isFromHome = this.$route.params.isFromHome
      if (isFromHome) {
        this.$router.go(-1)
      } else {
        this.$router.push({ name: 'category' })
      }
    },

    clear () {
      this.keywords = ''
      this.changeKey(this.keywords)
    }
  }
}
</script>

<style scoped lang="scss">
  .product-list-header {
    display: flex;
    height: 2rem;
    padding: 00.25rem;
    justify-content: space-between;
    align-items: center;
    border-bottom: 1px solid #f5f5f5;
    background: #fff;
    .slot{width:1.6rem;font-size:.8rem}
  }
  .common-search{
    flex:1;
    padding: 0.35rem 0.3rem 0.35rem 0;
  }
  .common-search input {
    box-sizing: border-box;
    width: 100%;
    height: 1.6rem;
    border-radius: 0.2rem;
    background: #EDEDED url(../../../assets/image/home/icon_search.png) no-repeat 0.6rem center;
    background-size:0.55rem;
    font-size:0.7rem;
    color: #999;
    padding-left:1.6rem;
    border: 0;
  }
  .right{width:2.6rem;height: 1.6rem;}
</style>
