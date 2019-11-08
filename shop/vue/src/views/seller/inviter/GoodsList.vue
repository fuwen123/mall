<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="商品分销管理" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
                <mt-button slot="right"  @click="goAdd">新增</mt-button>
            </mt-header>
        </div>
        <div  v-for="item in goods_list" :key="item.goods_commonid" @click="onEdit(item.goods_commonid)" class="mb-5">
            <mt-cell :title="item.goods_name" class="pt-5 pb-5">
                <span class="inviter_info"><em>已分销{{item.inviter_total_quantity}}件</em><em>总金额{{item.inviter_total_amount}}</em><em>总佣金{{item.inviter_amount}}</em></span>
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
    </div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import { getInviterGoodsList } from '../../../api/sellerInviter'
export default {
  name: 'GoodsList',
  data () {
    return {
      // 分销商品列表
      goods_list: []
    }
  },
  created: function () {
    this.getGoodsList()
  },
  methods: {
    goAdd () {
      this.$router.push({ name: 'SellerInviterGoodsForm', query: { action: 'add' } })
    },
    onEdit (goods_commonid) {
      this.$router.push({ name: 'SellerInviterGoodsForm', query: { goods_commonid: goods_commonid } })
    },
    getGoodsList () {
      getInviterGoodsList().then(res => {
        this.goods_list = res.result.goods_list
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  }
}
</script>

<style scoped>
.inviter_info{width:4.2rem;}
.inviter_info em{line-height:1rem;display:block;width:100%;}
</style>
