<template>
  <div class="home-base">
    <!-- 公共头 -->
    <van-nav-bar :title="$route.meta.head.title"
                 :left-arrow="$route.meta.head.showBack || false"
                 @click-left="onClickLeft"
                 v-if="$route.meta.head && $route.meta.head.show" />
    <!-- 公共头 end -->

    <!-- 内容 -->
    <keep-alive>
      <!-- 缓存视图 -->
      <router-view v-if="$route.meta.keepAlive"
                   ref="childView"></router-view>
    </keep-alive>

    <!--  非缓存视图 -->
    <router-view v-if="!$route.meta.keepAlive"></router-view>

    <!-- 底部导航 -->
    <div v-if="$route.meta.footer.show"
         class='common-footer-wrap'>
      <mt-tabbar v-model='home_selected'
                 fixed
                 class="common-footer">
        <mt-tab-item class='item-wrap'
                     :class="{'active':$route.name=='HomeIndex'}"
                     id='home_index'>
          <router-link :to="{ name: 'HomeIndex'}"
                       class='item'><i class='iconfont icon-homepage' /><span class='text'>首页</span></router-link>
        </mt-tab-item>
        <mt-tab-item class='item-wrap'
                     :class="{'active':$route.name=='HomeGoodsclass'}"
                     id='home_goodsclass'>
          <router-link :to="{ name: 'HomeGoodsclass'}"
                       class='item'><i class="iconfont icon-fenlei" /><span class='text'>分类</span></router-link>
        </mt-tab-item>
        <mt-tab-item class='item-wrap'
                     :class="{'active':$route.name=='HomeSearch'}"
                     id='home_search'>
          <router-link :to="{ name: 'HomeSearch'}"
                       class='item'><i class="iconfont icon-sousuo1" /><span class='text'>搜索</span></router-link>
        </mt-tab-item>
        <mt-tab-item class='item-wrap'
                     :class="{'active':$route.name=='HomeCart'}"
                     id='home_cart'>
          <router-link :to="{ name: 'HomeCart'}"
                       class='item'><i class='iconfont icon-31gouwuche' /><span class='text'>购物车</span></router-link>
        </mt-tab-item>
        <mt-tab-item class='item-wrap'
                     :class="{'active':$route.name=='MemberIndex'}"
                     id='member_index'>
          <router-link :to="{ name: 'MemberIndex'}"
                       class='item'><i class='iconfont icon-people' /><span class='text'>我的</span></router-link>
        </mt-tab-item>
      </mt-tabbar>
    </div>
  </div>
</template>

<script>

export default {
  name: 'HomeBase',
  data () {
    return {
      home_selected: 'home_index'
    }
  },
  computed: {

  },
  created: function () {

  },

  methods: {
    /**
     * 点击头部返回
     */
    onClickLeft () {
      let back = this.$route.meta.head.back || -1 // 默认返回上一页
      if (back === -1) {
        this.$router.go(back)
      } else {
        // back可以是子页面的方法的名称，点击的时候触发子页面方法名
        this.$refs.childView[back] && this.$refs.childView[back]()
      }
    }
  }
}
</script>

<style lang="scss" scoped>
/deep/ .van-icon.van-icon-arrow-left {
  color: #333333 !important;
}

/deep/ .van-nav-bar__title {
  color: #000000;
  font-weight: bold;
}
</style>
