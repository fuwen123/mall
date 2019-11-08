<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header class="common-header" title="付款">
                <mt-button icon="back" slot="left" @click="goBack"></mt-button>
            </mt-header>
        </div>
        <div v-if="pay_info">
            <div class="top-wrapper">
                <div class="row-wrapper title-wrapper">
                    <i class="icon iconfont">&#xe6d2;</i>
                    <label class="title">订单提交成功，请选择付款方式</label>
                </div>
                <div class="row-wrapper subtitle-wrapper">
                    <label class="subtitle">合计：</label>
                    <label class="price">￥ {{pay_info.pay_amount}}</label>
                </div>
            </div>

            <div class="item-wrapper" v-on:click="payType(item.payment_code)" v-for="(item,index) in payment_list" :key="index">
                <div class="item-left-wrapper">
                    <i class="icon"></i>
                    <label class="item-title">{{item.payment_name}}</label>
                </div>
                <div class="item-right-wrapper">
                    <i class="item-select iconfont" v-if="payment_info.payment_code == item.payment_code">&#xe69d;</i>
                </div>
            </div>
        </div>
        <mt-button class="ds-button-large" type="primary" v-on:click="submit">立即付款</mt-button>
    </div>
</template>

<script>
import { getOrderpayInfo } from '../../../api/memberBuy'
import { getVrOrderpayInfo } from '../../../api/memberVrBuy'
import { mapState } from 'vuex'
import { pay,getPaymentList } from '../../../api/memberPayment'
import { getRechargeInfo } from '../../../api/memberRecharge'
import { Toast } from 'mint-ui'
import { loadScript } from '../../../util/common'
export default {
  name: 'pay',
  data () {
    return {
      pay_sn: this.$route.query.pay_sn ? this.$route.query.pay_sn : '',
      pay_type: this.$route.query.pay_type ? this.$route.query.pay_type : '',
      // 订单支付显示的数据
      pay_info: null,
      // 提交支付需要用到的数据
      payment_info: {
        password: '', // 支付密码
        rcb_pay: 0, // 是否使用充值卡进行支付
        pd_pay: 0, // 是否使用预存款进行支付
        payment_code: 'alipay_h5', // 支付方式
        
      },
      payment_list:[],
    }
  },
  computed: {
    ...mapState({
      token: state => state.member.token

    })
  },
  created: function () {
    
    this.getPaymentList()
    this.getMemberPayInfo()
  },
  methods: {
    goBack () {
      this.$router.go(-1)
    },
    payType (code) {
      this.payment_info.payment_code = code
    },
		getPaymentList(){
    let _this = this
    
			getPaymentList().then(res=>{
				this.payment_list=res.result.payment_list
				if (this.payment_list.length) {
				  this.payment_info.payment_code = this.payment_list[0].payment_code
				}
        loadScript('weixin', 'https://res.wx.qq.com/open/js/jweixin-1.3.2.js', function () {
          wx.miniProgram.getEnv(function (res) {
            let indexof = -1
            let temp = _this.payment_list
            let j = temp.indexOf('allinpay_h5')

            if (res.miniprogram) {
              indexof = temp.indexOf('wxpay_jsapi')
              if (j >= 0) {
                temp[j] = 'allinpay_h5_W06'
              }
            } else {
              indexof = temp.indexOf('wxpay_minipro')
              if (j >= 0) {
                temp[j] = 'allinpay_h5_W02'
              }
            }
            if (indexof > -1) {
              temp.splice(indexof, 1)
            }
            if (indexof > -1 || j > -1) {
              _this.payment_list = temp
            }
          })
        })
			}).catch(function(error){
				Toast(error.message)
			})
		},
    getMemberPayInfo () {
      if (this.pay_type === 'pay_new') {
        let _this = this
        getOrderpayInfo(
          this.pay_sn
        ).then((res) => {
          if (res.code === 10000) {
            this.pay_info = res.result.pay_info

          }
        }).catch(function (error) {
          if (error.code === 12001) { // 订单已经支付
            _this.$router.push({ name: 'MemberOrderList' })
          } else {
            Toast(error.message)
          }
        })
      } else if (this.pay_type === 'vr_pay_new') {
        let _this = this
        getVrOrderpayInfo(
          this.pay_sn
        ).then((res) => {
          if (res.code === 10000) {
            this.pay_info = res.result.pay_info
 
          }
        }).catch(function (error) {
          if (error.code === 12001) { // 订单已经支付
            _this.$router.push({ name: 'MemberVrOrderList' })
          } else {
            Toast(error.message)
          }
        })
      } else if (this.pay_type === 'pd_pay') {
        getRechargeInfo(
          this.pay_sn
        ).then((res) => {
          if (res.code === 10000) {
            this.pay_info = { pay_amount: res.result.pdinfo.pdr_amount }
     
          }
        }).catch(function (error) {
          Toast(error.message)
        })
      }
    },
    submit () {
      if (this.payment_info.payment_code == 'wxpay_minipro') {
        wx.miniProgram.redirectTo({ url: '../pay/pay?action=' + this.pay_type + '&key=' + this.token + '&pay_sn=' + this.pay_sn + '&password=' + this.payment_info.password + '&rcb_pay=' + this.payment_info.rcb_pay + '&pd_pay=' + this.payment_info.pd_pay + '&payment_code=' + this.payment_info.payment_code })
        return
      }
      pay(
        this.pay_sn, this.pay_type, this.payment_info, this.token
      ).then((res) => {
        if (res.code) {
          if (res.code === 10000) {
            if (this.pay_type === 'pay_new') {
              this.$router.push({ name: 'MemberOrderList' })
            } else if (this.pay_type === 'vr_pay_new') {
              this.$router.push({ name: 'MemberVrOrderList' })
            } else if (this.pay_type === 'pd_pay') {
              this.$router.push({ name: 'MemberRechargeList' })
            }
          } else {
            Toast(res.message)
          }
        } else {
          document.write(res)
        }
      }).catch(function (error) {
        if (error.message) {
          Toast(error.message)
        } else {
          document.write(error)
        }
      })
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
        background-color: $mainbgColor;
    }
    .top-wrapper {
        height: 6rem;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        background-color: #fff;
        margin-bottom: 0.75rem;
    }
    .row-wrapper {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
    }
    .title-wrapper {
        height: 3.2rem;
    }
    .subtitle-wrapper {
        height: 1.5rem;
        margin-top: 0.5rem;
    }
    .icon {
        width: 0.9rem;
        height: 0.9rem;
        margin-left: 0.8rem;
        margin-right: 0.5rem;
    }
    .title {
        font-size: 0.8rem;
        color: #3C3C3C;
    }
    .subtitle {
        font-size: 0.8rem;
        color: #4D4D4D;
        margin-left: 2.5rem;
    }
    .price {
        font-size: 0.8rem;
        color: $primaryColor;
    }
    .item-wrapper{
        height: 3.1rem;
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
        background-color: #fff;
        border-bottom: 1px solid $lineColor;
    }
    .item-wrapper {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: stretch;
        background-color: #fff;
    }
    .item-left-wrapper {
        flex: 1;
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
    }
    .item-right-wrapper {
        flex: 1;
        display: flex;
        flex-direction: row;
        justify-content: flex-end;
        align-items: center;
    }
    .item-title {
        font-size: 0.8rem;
        color: #3C3C3C;
    }
    .item-subtitle {
        font-size: 0.8rem;
        color: #8C8C8C;
        margin-right:0.25rem;
        text-align: right;
    }
    .item-select {
        width: 0.8rem;
        height: 0.8rem;
        margin-right: 1.1rem;
    }
    .indicator {
        width: 0.7rem;
        height: 0.7rem;
        margin-left:0.25rem;
        margin-right: 0.8rem;
    }
    .action-wrapper {
        display: flex;
        flex-direction: row;
        justify-content: center;
        align-items: center;
        height: 2.5rem;
        margin-top: 2.25rem;
    }
</style>
