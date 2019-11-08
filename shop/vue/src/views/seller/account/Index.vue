<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="子帐号管理" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div  @click="goSellerAccountList">
            <mt-cell title="账户列表">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
        <div  @click="goSellerAccountGroupList">
            <mt-cell title="账户组">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
        <div  @click="goSellerLogList">
            <mt-cell title="账户日志">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
        <div  @click="goSellerCostList">
            <mt-cell title="店铺消费">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
    </div>
</template>

<script>
export default {
  name: 'Index',
  methods: {
    goSellerAccountList () {
      this.$router.push({ name: 'SellerAccountList' })
    },
    goSellerAccountGroupList () {
      this.$router.push({ name: 'SellerAccountGroupList' })
    },
    goSellerLogList () {
      this.$router.push({ name: 'SellerLogList' })
    },
    goSellerCostList () {
      this.$router.push({ name: 'SellerCostList' })
    }
  }
}
</script>

<style scoped>

</style>
