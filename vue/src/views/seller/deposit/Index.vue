<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="店铺保证金" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div class="header mb-5">
            <p>应缴保证金</p>
            <h2>{{store_info.store_payable_deposit}}</h2>
            <h5>已缴保证金：{{store_info.store_avaliable_deposit}}</h5>
            <h5>审核保证金：{{store_info.store_freeze_deposit}}</h5>
        </div>
        <div  @click="goSellerDepositList">
            <mt-cell title="保证金明细">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
        <div  @click="goSellerDepositWithdrawList">
            <mt-cell title="保证金审核">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
        <div @click="addDeposit">
            <mt-cell title="补缴保证金">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
        <div @click="reduceDeposit">
            <mt-cell title="取出保证金">
                <i class="iconfont">&#xe650;</i>
            </mt-cell>
        </div>
    </div>
</template>

<script>
import { Toast, MessageBox } from 'mint-ui'
import { getSellerInfo } from '../../../api/seller'
import { addSellerDeposit, reduceSellerDeposit } from '../../../api/sellerDeposit'
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
    // 店铺保证金明细
    goSellerDepositList () {
      this.$router.push({ name: 'SellerDepositList' })
    },
    // 保证金审核列表
    goSellerDepositWithdrawList () {
      this.$router.push({ name: 'SellerDepositWithdrawList' })
    },
    // 申请提现
    addDeposit () {
      MessageBox.prompt('请输入补缴金额', '').then(({ value, action }) => {
        addSellerDeposit(value).then(res => {
          this.getSellerInfo()
          Toast(res.message)
        }).catch(function (error) {
          Toast(error.message)
        })
      })
    },
    reduceDeposit () {
      MessageBox.prompt('请输入取出金额', '').then(({ value, action }) => {
        reduceSellerDeposit(value).then(res => {
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
    .header h5 {font-size:.8rem;text-align:center;line-height:2rem;}
</style>
