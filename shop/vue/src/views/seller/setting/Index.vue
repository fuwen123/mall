<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="设置" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div  @click="goSettingInfo">
            <mt-cell title="店铺管理">
                <i class="iconfont">&#xe69f;</i>
            </mt-cell>
        </div>

        <mt-button class="ds-button-large" type="primary" @click="logout">退出登录</mt-button>
    </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import { Toast } from 'mint-ui'
import { logout } from '../../../api/seller'
export default {
  name: 'Index',
  computed: {
    ...mapState({
      seller: state => state.seller.info
    })
  },
  methods: {
    ...mapMutations({
      sellerLogout: 'sellerLogout'
    }),
    logout () {
      logout(this.seller.seller_name).then(res => {
        this.sellerLogout()
        this.$router.push({ name: 'HomeSellerLogin' })
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    goSettingInfo () {
      this.$router.push({ name: 'SellerSettingInfo' })
    }

  }
}
</script>

<style scoped>
    .ds-button-large{margin-top: 1rem;}
</style>
