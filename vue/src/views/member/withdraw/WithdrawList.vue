<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header title="提现明细" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        <mt-button slot="right"  @click="goAdd">新增</mt-button>
      </mt-header>
    </div>
    <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
    <div
      class="list-wrapper"
      v-if="withdraw_list && withdraw_list.length"

    >
      <div
        class="container"
        v-for="(item, index) in withdraw_list"
        :key="item.pdc_id"
      >
        <div class="top-wrapper">
          <div class="left-wrapper">
            <label class="status">{{
              item.pdc_payment_state_text
              }}</label>
            <label class="title">{{ item.pdc_bank_user }}：{{ item.pdc_bank_no }}</label>
          </div>
          <div class="right-wrapper">
            <label class="price">￥{{ item.pdc_amount }}</label>
            <label class="subtitle">提现金额</label>
          </div>
          <div class="top-line"></div>
        </div>
        <div class="center-wrapper">
          <label class="desc">申请时间：{{ item.pdc_addtime_text }}</label>
        </div>

      </div>
    </div>
    <empty-record v-else-if="withdraw_list && !withdraw_list.length"></empty-record>
  </div>
    <!--身份验证-->
    <mt-popup v-model="sendAuthCode" position="right" class="common-popup-wrapper">
      <div class="common-header-wrap">
        <mt-header title="身份验证" class="common-header">
          <mt-button slot="left" icon="back" @click="sendAuthCode=false"></mt-button>
        </mt-header>
      </div>
      <div class="common-popup-content">
        <common-send-code @checkSuccess="checkSuccess" />
      </div>
    </mt-popup>

    <!--申请提现-->
    <mt-popup v-model="addWithdrawVisible" position="right" class="common-popup-wrapper">
      <div class="common-header-wrap">
        <mt-header title="申请提现" class="common-header">
          <mt-button slot="left" icon="back" @click="addWithdrawVisible=false"></mt-button>
        </mt-header>
      </div>
      <div class="common-popup-content">
        <mt-field class="menu-item" label="提现金额" v-model="amount" />
        <mt-field class="menu-item" label="支付密码" type="password" v-model="password" />
        <mt-cell title="收款银行">
          <mt-radio
                  v-model="memberbank_id"
                  :options="memberbankOptions">
          </mt-radio>
        </mt-cell>

        <mt-button type="primary" @click='addWithdraw' class="ds-button-large">提交</mt-button>
      </div>
    </mt-popup>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import { getWithdrawList, addWithdraw } from '../../../api/memberWithdraw'
import { getBankList } from '../../../api/memberBank'
import { Toast, Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import CommonSendCode from '../common/CommonSendCode'
export default {
  name: 'BalanceHistory',
  components: {
    EmptyRecord,
    CommonSendCode
  },
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      amount: '',
      memberbankOptions: [],
      memberbank_id: '',
      password: '',
      addWithdrawVisible: false,
      sendAuthCode: false,
      wrapperHeight: 0,
      withdraw_list: false
    }
  },
  created () {
    getBankList().then(res => {
      let temp = res.result.bank_list
      for (var i in temp) {
        let item = temp[i]
        this.memberbankOptions.push({
          label: item.memberbank_no,
          value: item.memberbank_id
        })
      }
    }).catch(function (error) {
      Toast(error.message)
    })
  },
  computed: {
    ...mapState({
      user: state => state.member.info
    })
  },
  mounted () {
    this.wrapperHeight = document.documentElement.clientHeight - 90
  },
  methods: {
    addWithdraw () {
      addWithdraw(this.amount, this.memberbank_id, this.password).then(res => {
        Toast(res.message)
        this.$router.go(0)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    checkSuccess () {
      this.addWithdrawVisible = true
    },
    goAdd () {
      if (!this.user.member_mobilebind && !this.user.member_emailbind) {
        Toast('请先绑定手机或邮箱')
        this.$router.push({ name: 'MemberSetting' })
      } else {
        this.sendAuthCode = true
      }
    },
    goBack () {
      this.$router.go(-1)
    },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getWithdrawList(true)
      }
    },
    getWithdrawList () {
      Indicator.open()

      getWithdrawList(this.params).then(res => {
        Indicator.close()
        if (res.result.hasmore) {
          this.isMore = true
        } else {
          this.isMore = false
        }

        let temp = res.result.list
        if (temp) {
          if (!this.withdraw_list) {
            this.withdraw_list = temp
          } else {
            this.withdraw_list = this.withdraw_list.concat(temp)
          }
        }
      }).catch(function (error) {
        Indicator.close()
        Toast(error.message)
      })
    }
  }
}
</script>

<style scoped lang="scss">
.container {
  height: 100%;
  display: flex;
  position: relative;
  flex-direction: column;
  justify-content: flex-start;
  align-items: stretch;

}
.header {
  border-bottom: 1px solid #e8eaed;
}
.topList {
  position: fixed;
  width: 100%;
  margin-top:2.2rem;
  height:2rem;
  z-index: 100;
}
.list-wrapper {
  .container {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: stretch;
    background-color: #fff;
  }
  .top-wrapper {
    position: relative;
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-items: stretch;
  }
  .left-wrapper {
    flex: 1;
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: stretch;
  }
  .right-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: stretch;
  }
  .status {
    font-size:0.7rem;
    color: #5296db;
    margin-left:0.75rem;
    margin-top:0.65rem;
  }
  .title {
    font-size:0.6rem;
    color: #666666;
    margin-left:0.75rem;
    margin-top:0.25rem;
    margin-right:0.5rem;
    margin-bottom:0.2rem;
  }
  .price {
    font-size:0.9rem;
    color:$primaryColor;
    margin-right:0.75rem;
    margin-top:0.5rem;
    text-align: right;
  }
  .subtitle {
    font-size:0.6rem;
    color: #999999;
    margin-right:0.75rem;
    margin-top:0.25rem;
    margin-bottom:0.5rem;
    text-align: right;
  }
  .top-line {
    position: absolute;
    left:0.75rem;
    bottom: 0;
    right: 0;
    height: 1px;
    background-color: #e8eaed;
  }
  .desc {
    color: #999999;
    font-size:0.6rem;
    margin-left:0.75rem;
    margin-top:0.25rem;
  }
  .center-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: stretch;
    border-bottom: 1px solid #e8eaed;
    padding-bottom:0.25rem;
  }
}
.mint-radiolist {
  display: flex;
  .mint-cell {
    flex: 1;
    .mint-radio-input:checked + .mint-radio-core {
      background-color: #e93b3d !important;
      border-color: #e93b3d !important;
    }
  }
}
</style>
