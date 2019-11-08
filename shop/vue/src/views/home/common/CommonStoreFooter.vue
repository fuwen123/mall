<template>
  <div class="home-base">
    <router-view/>
    <div class='common-footer-wrap'>
      <mt-tabbar v-model='home_selected' fixed class="common-footer">
        <mt-tab-item class='item-wrap' :class="{'active':$route.name=='HomeStoredetail'}" id='home_index'><router-link :to="{ name: 'HomeStoredetail',query:{id:store_id}}" class='item'><i class='iconfont icon-homepage' /><span class='text'>店铺首页</span></router-link></mt-tab-item>
        <mt-tab-item class='item-wrap' :class="{'active':$route.name=='HomeStoreGoodslist'}" id='home_goodsclass'><router-link :to="{ name: 'HomeStoreGoodslist',query:{id:store_id}}" class='item'><i class="iconfont icon-31quanbushangpin" /><span class='text'>全部商品</span></router-link></mt-tab-item>
        <mt-tab-item class='item-wrap' :class="{'active':$route.name=='HomeStoreGoodsclass'}" id='home_search'><router-link :to="{ name: 'HomeStoreGoodsclass',query:{id:store_id}}" class='item'><i class="iconfont icon-sousuo1" /><span class='text'>店内搜索</span></router-link></mt-tab-item>
        <mt-tab-item class='item-wrap' :class="{'active':$route.name=='HomeCart'}" id='home_cart'><router-link :to="{ name: 'HomeCart'}" class='item'><i class='iconfont icon-31gouwuche' /><span class='text'>购物车</span></router-link></mt-tab-item>
        <mt-tab-item class='item-wrap' :class="{'active':$route.name=='MemberIndex'}" id='member_index'><router-link :to="{ name: 'MemberIndex'}" class='item'><i class='iconfont icon-people' /><span class='text'>我的</span></router-link></mt-tab-item>
      </mt-tabbar>
    </div>
  </div>
</template>

<script>

export default {
  name: 'CommonStoreFooter',
  data () {
    return {
      home_selected: 'home_index'
    }
  },
  props: ['store_id'],
  computed: {

  },
  created: function () {

  }
}
</script>
