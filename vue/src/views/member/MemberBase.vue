<template>
  <div class="member-base">
    <router-view />
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
import { mapState } from 'vuex'
export default {
  name: 'MemberBase',
  data () {
    return {
      home_selected: 'member_index'
    }
  },
  computed: {
    ...mapState({
      user: state => state.member.info
    })
  },
  created: function () {
    if (!this.user) {
      this.$router.push({ name: 'HomeMemberLogin' })
    }
  }
}
</script>
