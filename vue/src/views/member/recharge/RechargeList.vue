<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header title="充值明细" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        <mt-button slot="right"  @click="goAdd">充值</mt-button>
      </mt-header>
    </div>
    <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
      <div class="list-wrapper" v-if="recharge_list && recharge_list.length">
        <div
                class="container"
                v-for="(item, index) in recharge_list"
                :key="item.pdr_id"
                @click="goToPay(item.pdr_payment_state,item.pdr_sn)"
        >
          <div class="top-wrapper">
            <div class="left-wrapper">
              <label class="status">{{
                item.pdr_payment_state_text
                }}</label>
              <label class="title">充值单号：{{ item.pdr_sn }}</label>
            </div>
            <div class="right-wrapper">
              <label class="price">￥{{ item.pdr_amount }}</label>
              <label class="subtitle">充值金额</label>
            </div>
            <div class="top-line"></div>
          </div>
          <div class="center-wrapper">
            <label class="desc">创建时间：{{ item.pdr_addtime_text }}</label>
          </div>
        </div>
      </div>
      <empty-record v-else-if="recharge_list && !recharge_list.length"></empty-record>
    </div>
  </div>
</template>

<script>
import { getRechargeList, addRecharge } from '../../../api/memberRecharge'
import { Toast, MessageBox, Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
export default {
  name: 'BalanceHistory',
  components: {
    EmptyRecord
  },
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      wrapperHeight: 0,

      recharge_list: false

    }
  },
  created () {
  },

  mounted () {
    this.wrapperHeight = document.documentElement.clientHeight - 90
  },
  methods: {
    goAdd () {
      MessageBox.prompt('请输入充值金额', '').then(({ value, action }) => {
        addRecharge(value).then(res => {
          this.goToPay(0, res.result.pay_sn)
        }).catch(function (error) {
          Toast(error.message)
        })
      })
    },
    goBack () {
      this.$router.go(-1)
    },
    goToPay (state, paySn) {
      if (state == 0) {
        this.$router.push({ name: 'MemberBuyPay', query: { pay_sn: paySn, pay_type: 'pd_pay' } })
      }
    },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getRechargeList(true)
      }
    },
    getRechargeList () {
      Indicator.open()

      getRechargeList(this.params).then(res => {
        Indicator.close()
        if (res.result.hasmore) {
          this.isMore = true
        } else {
          this.isMore = false
        }

        let temp = res.result.list
        if (temp) {
          if (!this.recharge_list) {
            this.recharge_list = temp
          } else {
            this.recharge_list = this.recharge_list.concat(temp)
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
  margin-top: 2.2rem;
  height: 2rem;
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
    font-size: 0.7rem;
    color: #5296db;
    margin-left: 0.75rem;
    margin-top: 0.65rem;
  }
  .title {
    font-size: 0.6rem;
    color: #666666;
    margin-left: 0.75rem;
    margin-top:0.25rem;
    margin-right: 0.5rem;
    margin-bottom: 0.2rem;
  }
  .price {
    font-size: 0.9rem;
    color: $primaryColor;
    margin-right: 0.75rem;
    margin-top: 0.5rem;
    text-align: right;
  }
  .subtitle {
    // width: 4rem;
    font-size: 0.6rem;
    color: #999999;
    margin-right: 0.75rem;
    margin-top:0.25rem;
    margin-bottom: 0.5rem;
    text-align: right;
  }
  .top-line {
    position: absolute;
    left: 0.75rem;
    bottom: 0;
    right: 0;
    height: 1px;
    background-color: #e8eaed;
  }
  .desc {
    color: #999999;
    font-size: 0.6rem;
    margin-left: 0.75rem;
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
</style>
