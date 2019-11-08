<template>
  <div class="seller-base">
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
                       class='item'><i class='iconfont icon-homepage' /><span class='text'>返回商城</span></router-link>
        </mt-tab-item>
        <mt-tab-item class='item-wrap'
                     :class="{'active':$route.name=='SellerOrderList'}"
                     id='seller_order'>
          <router-link :to="{ name: 'SellerOrderList'}"
                       class='item'><i class='iconfont icon-quanbudingdan1' /><span class='text'>订单管理</span></router-link>
        </mt-tab-item>
        <mt-tab-item class='item-wrap'
                     :class="{'active':$route.name=='SellerIndex'}"
                     id='seller_index'>
          <router-link :to="{ name: 'SellerIndex'}"
                       class='item'><i class='iconfont icon-people' /><span class='text'>店铺中心</span></router-link>
        </mt-tab-item>
      </mt-tabbar>
    </div>
  </div>
</template>

<script>
import { mapState } from 'vuex'
export default {
  name: 'SellerBase',
  data () {
    return {
      home_selected: 'seller_index'
    }
  },
  computed: {
    ...mapState({
      user: state => state.seller.info
    })
  },
  created: function () {
    if (!this.user) {
      this.$router.push({ name: 'HomeSellerLogin' })
    }
  }
}
</script>
