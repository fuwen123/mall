<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="子账户管理" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
                <mt-button slot="right"  @click="goAdd">新增</mt-button>
            </mt-header>
        </div>
        <div  v-for="item in sellerList" v-bind:key="item.seller_id" @click="onEdit(item.seller_id)" >
            <mt-cell :title="item.seller_name">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
        <empty-record v-if="sellerList && !sellerList.length"></empty-record>
    </div>
</template>

<script>
import { Indicator, Toast } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getSellerAccountList } from '../../../api/sellerAccount'
export default {
  name: 'AccountList',
  components: {
    EmptyRecord
  },
  data () {
    return {
      sellerList: false // 用户列表
    }
  },
  created: function () {
    this.getAccountList()
  },
  methods: {
    getAccountList () {
      Indicator.open()
      getSellerAccountList().then(res => {
        Indicator.close()
        this.sellerList = res.result.seller_list
      }).catch(function (error) {
        Indicator.close()
        Toast(error.message)
      })
    },
    onEdit (sellerId) {
      this.$router.push({ name: 'SellerAccountForm', query: { seller_id: sellerId } })
    },
    goAdd () {
      this.$router.push({ name: 'SellerAccountForm', query: { action: 'add' } })
    }
  }

}
</script>

<style scoped>

</style>
