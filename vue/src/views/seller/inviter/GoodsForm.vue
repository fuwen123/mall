<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header :title="getTitle" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
                <mt-button slot="right"  @click="onDelete" v-if="!isAddMode">删除</mt-button>
            </mt-header>
        </div>
        <div v-if="isAddMode">
            <div @click="popupSelect = true">
            <mt-cell :title="getSelecedTitle">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
            </div>
            <mt-popup v-model="popupSelect" position="right" class="common-popup-wrapper" :modal="false">
                <div class="common-header-wrap">
                    <mt-header title="选择商品" class="common-header">
                        <mt-button slot="left" icon="back" @click.native="popupSelect = false"></mt-button>
                    </mt-header>
                </div>
                <div class="select-goods mt-5">
                    <ul>
                        <li v-for="item in selectGoodsList" v-bind:key="item.goods_commonid" @click="selectedGoods(item)">
                            <div class="p-img">
                                <img :src="item.goods_image"/>
                            </div>
                            <div class="p-info">
                                <p class="name">{{item.goods_name}}</p>
                                <p class="price">{{item.goods_price}}</p>
                            </div>
                        </li>
                    </ul>
                </div>
            </mt-popup>
        </div>
        <div v-else>
            <mt-cell :title="getSelecedTitle"></mt-cell>
        </div>
        <mt-field label="1级佣金比例" v-model="goods.inviter_ratio_1"></mt-field>
        <mt-field label="2级佣金比例" v-model="goods.inviter_ratio_2"></mt-field>
        <mt-field label="3级佣金比例" v-model="goods.inviter_ratio_3"></mt-field>
        <mt-button class="ds-button-large" type="primary" v-on:click="submit">提交</mt-button>
    </div>
</template>

<script>
import { Toast, Indicator, MessageBox } from 'mint-ui'
import { searchGoodsList, addInviterGoods, editInviterGoods, getInviterGoodsInfo, delInviterGoods } from '../../../api/sellerInviter'
export default {
  name: 'GoodsForm',
  data () {
    return {
      // 弹出选择商品
      popupSelect: false,
      // 商品列表用于选择商品设置分拥比例
      selectGoodsList: [],
      // 关键词
      selectGoodsName: '',
      goods: {
        'goods_commonid': 0,
        'goods_name': '',
        'inviter_ratio_1': '',
        'inviter_ratio_2': '',
        'inviter_ratio_3': ''
      }
    }
  },
  created: function () {
    if (!this.isAddMode) {
      this.goods.goods_commonid = this.$route.query.goods_commonid
      getInviterGoodsInfo(this.goods.goods_commonid).then(res => {
        this.goods = res.result.info
      }).catch(function (error) {
        Toast(error.message)
      })
    } else {
      this.getSelectGoodsList()
    }
  },
  computed: {
    isAddMode () {
      let mode = this.$route.query.action
      if (mode === 'add') {
        return true
      } else {
        return false
      }
    },
    getTitle () {
      if (this.isAddMode) {
        return '新增分销商品'
      } else {
        return '修改分销商品'
      }
    },
    getSelecedTitle () {
      if (this.goods.goods_commonid) {
        return this.goods.goods_name
      } else {
        return '选择商品'
      }
    }
  },
  methods: {
    onDelete () {
      MessageBox.confirm('确定要取消该商品佣金设置吗？').then(action => {
        delInviterGoods(this.goods.goods_commonid).then(
          (response) => {
            this.$router.push({ name: 'SellerInviterGoodsList' })
          }, (error) => {
            Toast(error.message)
          })
      })
    },
    submit () {
      if (this.isAddMode) {
        Indicator.open()
        addInviterGoods(this.goods).then(
          (response) => {
            Indicator.close()
            Toast(response.message)
            this.$router.push({ name: 'SellerInviterGoodsList' })
          }, (error) => {
            Indicator.close()
            Toast(error.message)
          })
      } else {
        Indicator.open()
        editInviterGoods(this.goods).then(
          (response) => {
            Indicator.close()
            Toast(response.message)
            this.$router.push({ name: 'SellerInviterGoodsList' })
          }, (error) => {
            Indicator.close()
            Toast(error.message)
          })
      }
    },
    // 获取商品列表 用于设置产品分佣
    getSelectGoodsList () {
      searchGoodsList(this.selectGoodsName).then(res => {
        this.selectGoodsList = res.result.goods_list
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    // 选择商品
    selectedGoods (item) {
      this.goods.goods_name = item.goods_name
      this.goods.goods_commonid = item.goods_commonid
      // Close
      this.popupSelect = false
    }
  }
}
</script>

<style scoped  lang="scss">
.select-goods{overflow-y:scroll;}
.select-goods li{height:3rem;padding:.6rem;display:flex;}
.select-goods li .p-img{}
.select-goods li .p-img img{width:3rem;height: 3rem;}
.select-goods li .p-info{margin-left:1rem;}
.select-goods li .p-info p{line-height:1rem;}
.select-goods li .p-info .price{font-size:0.6rem;color:$primaryColor;height:2rem;overflow: hidden}
.select-goods li .p-info .name{font-size:0.7rem;}

</style>
