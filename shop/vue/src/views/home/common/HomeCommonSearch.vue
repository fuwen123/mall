<template>
  <div class="product-list-header" :class="{'logo':from}">
    <div class="slot" v-if='from=="home" && config' ><img :src='config.site_mobile_logo'></div>
    <div class="slot iconfont" @click="$router.go(-1)" v-else >&#xe64f;</div>
    <div class="common-search">
      <input type="text" placeholder="请输入您要搜索的商品" readonly="true" autocomplete="off" v-model="keyword" @click="onSearch">
    </div>
    <div class="right iconfont" @click="popupMore">&#xe680;<div v-if="showDot" class="dot"></div></div>
    <header-more v-show="popupVisible" :showDot="showDot"></header-more>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import { getChatCount } from '../../../api/memberChat'
import HeaderMore from '../../../components/HeaderMore'
export default {
  name: 'HomeCommonSearch',
  components: {
    HeaderMore
  },
  props: ['value', 'from'],
  watch: {
    value: function (value) {
      if (value) {
        this.keyword = value
      }
    }
  },
  data () {
    return {
      keyword: this.value ? this.value : '', // 关键字
      popupVisible: false,
      showDot: false
    }
  },
  created: function () {
    if (this.isOnline) {
      getChatCount().then(res => {
        if (res.result) {
          this.showDot['chat'] = true
        }
      })
    }
  },
  computed: {
    ...mapState({
      config: state => state.config.config,
      isOnline: state => state.member.isOnline
    })
  },
  methods: {
    onSearch () {
      this.$router.push({ name: 'HomeSearch', params: { isFromHome: true } })
    },
    popupMore () {
      if (!this.popupVisible) {
        this.popupVisible = true
      } else {
        this.popupVisible = false
      }
    }
  }
}
</script>

<style scoped lang="scss">
.dot{position:absolute;width:.5rem;height:.5rem;background:red;border-radius:50%;top:.2rem;right:0;}
  .product-list-header.logo{
  .slot{width:2.5rem;text-align:center;line-height:1.6rem;
    img{max-height:100%;max-width:100%;}
  }
  .common-search{padding: 0.35rem 0.3rem 0.35rem 0.3rem;}
  }
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
  .right{width:1.6rem;height: 1.6rem;text-align:center;line-height: 1.6rem;position:relative}
</style>
