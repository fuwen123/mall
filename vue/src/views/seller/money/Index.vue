<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="店铺资金" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div class="header mb-5">
            <p>可用余额</p>
            <h2>{{store_info.store_avaliable_money}}</h2>
            <h5>冻结金额：{{store_info.store_freeze_money}}</h5>
        </div>
        <div  @click="goSellerMoneyLogList">
            <mt-cell title="资金明细">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
        <div  @click="goSellerMoneyWithdrawList">
            <mt-cell title="提现列表">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
        <div @click="goSellerMoneyWithdrawForm">
            <mt-cell title="申请提现">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
    </div>
</template>

<script>
import { Toast, MessageBox } from 'mint-ui'
import { getSellerInfo } from '../../../api/seller'
import { addSellerMoneyWithdraw } from '../../../api/sellerMoney'
export default {
  name: 'Index',
  data () {
    return {
      store_info: {}
    }
  },
  created: function () {
    this.getSellerInfo()
  },
  methods: {
    getSellerInfo () {
      getSellerInfo().then(response => {
        if (response && response.result) {
          this.store_info = response.result.store_info
        }
      }
      ).catch(function (error) {
        Toast(error.message)
      })
    },
    // 店铺资金明细
    goSellerMoneyLogList () {
      this.$router.push({ name: 'SellerMoneyLogList' })
    },
    // 提现明细
    goSellerMoneyWithdrawList () {
      this.$router.push({ name: 'SellerMoneyWithdrawList' })
    },
    // 申请提现
    goSellerMoneyWithdrawForm () {
      MessageBox.prompt('请输入提现金额', '').then(({ value, action }) => {
        addSellerMoneyWithdraw(value).then(res => {
          this.getSellerInfo()
          Toast(res.message)
        }).catch(function (error) {
          Toast(error.message)
        })
      })
    }
  }
}
</script>

<style scoped>
.header{background: #e93b3d;width: 100%;position: relative;color: #fff;padding:2.2rem 0;}
.header p{font-size:.8rem;text-align:center;}
.header h2 {font-size:1.6rem;padding: .5rem 0;text-align:center;}
.header h5 {font-size:.8rem;text-align:center;}
</style>
