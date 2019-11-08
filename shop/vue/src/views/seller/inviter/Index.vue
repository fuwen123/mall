<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="店铺分销" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div  @click="goSellerInviterGoodsList">
        <mt-cell title="分销商品管理">
            <i class="iconfont">&#xe650;</i>
        </mt-cell>
        </div>
        <div  @click="goSellerInviterOrderList">
        <mt-cell title="分销业绩" @click="goSellerInviterOrderList">
            <i class="iconfont">&#xe650;</i>
        </mt-cell>
        </div>
    </div>
</template>

<script>
export default {
  name: 'Index',
  methods: {
    goSellerInviterGoodsList () {
      this.$router.push({ name: 'SellerInviterGoodsList' })
    },
    goSellerInviterOrderList () {
      this.$router.push({ name: 'SellerInviterOrderList' })
    }
  }
}
</script>

<style scoped>

</style>
