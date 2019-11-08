<template>
    <div class="container">
        <div class="top-wrapper">
            <div class="nav-item" id="left-nav-item" @click="goSettingIndex">
                <i class="nav-icon iconfont">&#xe607;</i>
            </div>
            <div class="nav-item" id="right-nav-item" @click="goNews()">
                <i class="nav-icon iconfont">&#xe6ce;</i>
                <span v-if="0"></span>
            </div>
            <div class="top-info-wrapper">
                <div class="avatar-wrapper" @click="goSettingIndex">
                    <img class="avatar" v-bind:src="seller.store_avatar" v-if="isOnline && seller && seller.store_avatar"/>
                </div>
                <label class="sellername" style="-webkit-box-orient:vertical" @click="goSettingIndex">{{sellername}}</label>
            </div>
        </div>
        <div class="order-header" @click="goSellerorder">
            <div class="order-header-item" id="order-item-left">
                <i class="order-header-icon iconfont">&#xe6de;</i>
                <label class="item-title order-header-title">订单管理</label>
            </div>
            <div class="order-header-item" id="order-item-right">
                <label class="order-subtitle">查看全部订单</label>
                <i class="indicator iconfont">&#xe650;</i>
            </div>
            <div class="order-header-line"></div>
        </div>
        <div class="order-wrapper">
            <index-order-item
                    class="order-item"
                    testAttr = 'SellerOrderList'
                    id='state_new'
                    iconfont="&#xe664;"
                    title="待付款"
                    :orderNumber = 'seller_info.order_nopay_count'>
            </index-order-item>
            <index-order-item
                    class="order-item"
                    testAttr = 'SellerOrderList'
                    id='state_pay'
                    iconfont="&#xe663;"
                    title="待发货"
                    :orderNumber = 'seller_info.order_noship_count'>
            </index-order-item>
            <index-order-item
                    class="order-item"
                    testAttr = 'SellerOrderList'
                    id='state_send'
                    iconfont="&#xe6a6;"
                    title="待收货"
                    :orderNumber = 'seller_info.order_noreceipt_count'>
            </index-order-item>
        </div>
        <div class="manage-wrapper mt-5">
            <index-manage-item
                    v-on:onclick="goGoodsonline"
                    iconfont="&#xe6e7;"
                    color="#ef5b9c"
                    title="商品管理">
            </index-manage-item>
            <index-manage-item
                    v-on:onclick="goGoodsclass"
                    iconfont="&#xe61d;"
                    color="#840228"
                    title="店铺分类">
            </index-manage-item>
            <index-manage-item
                    v-if="!seller.is_platform_store"
                    v-on:onclick="goMoney"
                    iconfont="&#xe697;"
                    color="#ef5b9c"
                    title="店铺资金">
            </index-manage-item>
            <index-manage-item
                    v-if="!seller.is_platform_store"
                    v-on:onclick="goDeposit"
                    iconfont="&#xe671;"
                    color="#ef5b9c"
                    title="店铺保证金">
            </index-manage-item>
            <index-manage-item
                    v-on:onclick="goVrorder"
                    iconfont="&#xe6de;"
                    color="#b36d41"
                    title="虚拟订单">
            </index-manage-item>
            <index-manage-item
                    v-on:onclick="goSellerbill"
                    iconfont="&#xe634;"
                    color="#2b6447"
                    title="结算管理">
            </index-manage-item>
            <index-manage-item
                    v-on:onclick="goSellerRefundList(1)"
                    iconfont="&#xe67a;"
                    color="#7d5886"
                    title="退款管理">
            </index-manage-item>
            <index-manage-item
                    v-on:onclick="goSellerRefundList(2)"
                    iconfont="&#xe67a;"
                    color="#7d5886"
                    title="退货管理">
            </index-manage-item>
            <index-manage-item
                    v-on:onclick="goSellerChatList"
                    iconfont="&#xe744;"
                    color="#fb6c4a"
                    title="聊天列表">
            </index-manage-item>
        </div>
        <div class="bottom-wrapper mt-5">
            <index-info-item
                    v-on:onclick="goSellerEvaluateList"
                    iconfont="&#xe6a7;"
                    title="评价管理">
            </index-info-item>
            <index-info-item
                    v-on:onclick="$router.push({name:'SellerComplaintList'})"
                    iconfont="&#xe67a;"
                    title="投诉管理">
            </index-info-item>
            <index-info-item
                    v-on:onclick="goConsult"
                    iconfont="&#xe744;"
                    title="咨询管理">
            </index-info-item>
            <index-info-item
                    v-on:onclick="goAccount"
                    iconfont="&#xe6b4;"
                    title="子账户管理">
            </index-info-item>
            <index-info-item
                    v-on:onclick="goSellerInviter"
                    iconfont="&#xe625;"
                    title="店铺分销">
            </index-info-item>
            <index-info-item
                    v-on:onclick="goSellerStatisticsGeneral"
                    iconfont="&#xe617;"
                    title="店铺概况统计">
            </index-info-item>
        </div>
    </div>
</template>

<script>
import { Toast } from 'mint-ui'
import IndexInfoItem from './IndexInfoItem'
import IndexOrderItem from './IndexOrderItem'
import IndexManageItem from './IndexManageItem'
import { getSellerInfo } from '../../../api/seller'
import { mapState, mapMutations } from 'vuex'
export default {
  name: 'Index',
  data () {
    return {
      store_info: {},
      seller_info: {},
      statics: {}
    }
  },
  components: {
    IndexInfoItem,
    IndexManageItem,
    IndexOrderItem
  },
  created: function () {
    if (this.isOnline) {
      getSellerInfo().then(response => {
        if (response && response.result) {
          this.seller_info = response.result.seller_info
          this.store_info = response.result.store_info
          this.statics = response.result.statics
        }
      }
      ).catch(function (error) {
        Toast(error.message)
      })
    }
  },
  computed: {
    ...mapState({
      isOnline: state => state.seller.isOnline,
      seller: state => state.seller.info
    }),
    sellername () {
      let title = '登录/注册'
      if (this.isOnline) {
        if (this.seller && typeof this.seller !== 'undefined' && JSON.stringify(this.seller) !== '{}') {
          title = this.seller.seller_name
        }
      }
      return title
    }
  },
  methods: {
    ...mapMutations({
      memberUpdate: 'memberUpdate'
    }),
    showLogin () {
      this.$router.push({ name: 'HomeSellerlogin' })
    },
    // 店铺资金 普通店铺显示
    goMoney () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerMoney' })
      } else {
        this.showLogin()
      }
    },
    // 店铺保证金 普通店铺显示
    goDeposit () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerDeposit' })
      } else {
        this.showLogin()
      }
    },
    // 店铺设置
    goSettingIndex () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerSettingIndex' })
      } else {
        this.showLogin()
      }
    },
    goNews () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerNoticeList' })
      } else {
        this.showLogin()
      }
    },
    // 子账户管理
    goAccount () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerAccount' })
      } else {
        this.showLogin()
      }
    },
    // 商品管理
    goGoodsonline () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerGoodsonline' })
      } else {
        this.showLogin()
      }
    },
    // 商家聊天列表
    goSellerChatList () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerChatList' })
      } else {
        this.showLogin()
      }
    },
    // 订单列表
    goSellerorder () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerOrderList' })
      } else {
        this.showLogin()
      }
    },
    // 退款管理
    goSellerRefundList (refundType) {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerRefundList', query: { refund_type: refundType } })
      } else {
        this.showLogin()
      }
    },
    // 评价管理
    goSellerEvaluateList () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerEvaluateList' })
      } else {
        this.showLogin()
      }
    },

    // 订单结算
    goSellerbill () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerBillList' })
      } else {
        this.showLogin()
      }
    },
    // 店铺统计
    goSellerStatisticsGeneral () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerStatisticsGeneral' })
      } else {
        this.showLogin()
      }
    },
    // 咨询管理
    goConsult () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerConsultList' })
      } else {
        this.showLogin()
      }
    },
    // 店铺分类
    goGoodsclass () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerGoodsClassList' })
      } else {
        this.showLogin()
      }
    },
    // 虚拟订单
    goVrorder () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerVrOrderList' })
      } else {
        this.showLogin()
      }
    },
    // 店铺分销
    goSellerInviter () {
      if (this.isOnline) {
        this.$router.push({ name: 'SellerInviter' })
      } else {
        this.showLogin()
      }
    }
  }

}
</script>

<style scoped lang='scss'>
    .container {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        background-color: #f0f2f5;
    .top-wrapper {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        height:8rem;
        background: linear-gradient(90deg,#f01414,#f01414);
    .top-info-wrapper {
        flex: 1;
        width: 100%;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
    }
    }
    .nav-item {
        position: absolute;
        display: flex;
        justify-content: center;
        align-items: center;
        top:0rem;
    span {
        width:0.4rem;
        height:0.4rem;
        background-color: #f0f2f5;
        border-radius: 50%;
        position: absolute;
        top:0.5rem;
        right: 0.5rem;
    }
    }
    #left-nav-item {
        left: 0;
    }
    #right-nav-item {
        right: 0;
    }
    .nav-icon {
        width:2rem;
        height:2rem;
        line-height:2rem;
        text-align: center;
        font-size:1rem;
        color:#fff;
    }
    .avatar-wrapper {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        width:4rem;
        height:4rem;
        border-radius:50%;
        background-color: #fff;
        margin-top:1rem;
    .avatar {
        width:3.6rem;
        height:3.6rem;
        border-radius:50%;
    }
    }
    .sellername {
        width: 100%;
        margin-top:.5rem;
        font-size:.8rem;
        color:rgba(255,255,255,1);
        text-align: center;
        margin-left: 0;
        margin-right: 0;
    }
    .info-wrapper {
        width: 100%;
        height: 2rem;
        display: inline-flex;
        flex-direction: row;
        justify-content: flex-start;
        align-content: stretch;
        background-color: rgba(0, 0, 0, 0.1);
    }
    .info-item {
        flex: 1;
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        color: #fff;
        font-size:.8rem;
    }
    .order-header {
        height:2rem;
        position: relative;
        display: flex;
        flex-direction: row;
        justify-content: space-around;
        align-content: stretch;
        background-color: #fff;
    }
    .order-header-item {
        flex: 1;
        display: flex;
        flex-direction: row;
        align-items: center;
    }
    #order-item-left {
        justify-content: flex-start;
        margin-left:1rem;
    }
    #order-item-right {
        justify-content: flex-end;
    }
    .order-header-line {
        position: absolute;
        height: 1px;
        left: 1rem;
        bottom:0;
        right: 1rem;
        background-color: #E8EAED;
    }
    .item-title {
        font-size:.7rem;
        color: #333;
    }
    .order-header-icon {
        width:1rem;
        height:1rem;
        line-height:1rem;
    }
    .order-header-title {
        margin-left:0.4rem;
    }
    .indicator {
        width:.6rem;
        height:.6rem;
        margin-left:1rem;
        margin-right:1rem;
    }
    .order-subtitle {
        font-size:.6rem;
        color: #7c7f88;
    }
    .order-wrapper {
        height:4rem;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: stretch;
        background-color: #fff;
    }
    .order-item {
        flex: 1;
    }
        .bottom-wrapper {
            display: flex;
            flex-direction: column;
            justify-content: flex-start;
            align-items: stretch;
            margin-bottom:2rem;
        }
        .manage-wrapper{background:#fff;}
    }
</style>
