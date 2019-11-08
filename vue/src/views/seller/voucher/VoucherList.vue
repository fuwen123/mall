<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header title="代金券列表" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        <mt-button slot="right" @click="$router.push({name:'SellerVoucherForm',query:{mode:'add'}})">新增</mt-button>
      </mt-header>
    </div>
    <div v-if="!seller.is_platform_store" class="notice">
      <span>套餐过期时间：<span v-if="current_quota" class="red">{{$moment.unix(current_quota.voucherquota_endtime).format('YYYY.MM.DD')}}</span><span class="red" v-else>您还没购买套餐</span></span>
      <mt-button class="btn" @click="addQuota(current_quota)" size="small" type="primary"><span v-if="current_quota">续期</span><span v-else>购买</span></mt-button>
    </div>
    <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
    <div
      class="list-wrapper"
      v-if="voucher_list && voucher_list.length"
    >
      <div @click="goVoucerEdit(item)" class="common-voucher common-voucher02" :class="(item.vouchertemplate_state != 1)?'disable':''" v-for="(item,index) in voucher_list" :key="item.vouchertemplate_id">
        <div class="par"><p>订单满{{item.vouchertemplate_limit}}元</p><sub class="sign">￥</sub><span>{{item.vouchertemplate_price}}</span></div>
        <div class="copy">有效期至<p>{{$moment.unix(item.vouchertemplate_enddate).format('YYYY.MM.DD')}}<br></p></div>
        <i></i>
      </div>

    </div>
    <empty-record v-else-if="voucher_list && !voucher_list.length"></empty-record>
  </div>
  </div>
</template>

<script>
import { getVoucherList, addQuota } from '../../../api/sellerVoucher'
import { Toast, Indicator, MessageBox } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { mapState, mapMutations } from 'vuex'
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

      voucher_list: false,
      current_quota: false,
      voucher_storetimes_limit: false,
      promotion_voucher_price: false,
    }
  },
  created () {

  },

  mounted () {
    this.wrapperHeight = document.documentElement.clientHeight - 90
  },
  computed: {
    ...mapState({
      seller: state => state.seller.info
    })

  },
  methods: {
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getVoucherList(true)
      }
    },
    goVoucerEdit (item) {
      if (item.vouchertemplate_state == 1 && !item.vouchertemplate_giveout) {
        this.$router.push({ name: 'SellerVoucherForm', query: { mode: 'edit', tid: item.vouchertemplate_id } })
      } else {
        Toast('当前代金券不可编辑')
      }
    },
    addQuota (type) {
      MessageBox.prompt('购买单位为月（30天），一次最多购买12个月，每月您需要支付' + this.promotion_voucher_price + '元，相关费用会在店铺的账期结算中扣除，每月最多发布活动' + this.voucher_storetimes_limit + '次', (type ? '续期' : '购买') + '套餐', { inputType: 'number' }).then(({ value, action }) => {
        addQuota(value).then(res => {
          Toast(res.message)
          this.current_quota = { voucherquota_endtime: res.result.voucherquota_endtime }
        }).catch(function (error) {
          Toast(error.message)
        })
      })
    },
    getVoucherList () {
      Indicator.open()

          getVoucherList(this.params).then(res => {
            Indicator.close()
            if (res.result.hasmore) {
              this.isMore = true
            } else {
              this.isMore = false
            }
            this.current_quota = res.result.current_quota
            this.promotion_voucher_price = res.result.promotion_voucher_price
            this.voucher_storetimes_limit = res.result.voucher_storetimes_limit
            let temp = res.result.vouchertemplate_list
            if (temp) {
              if (!this.voucher_list) {
                this.voucher_list = temp
              } else {
                this.voucher_list = this.voucher_list.concat(temp)
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
.common-voucher{margin:0.5rem auto}
  .notice{font-size:.7rem;padding:.5rem;background:#FCF8E3;color:#C09853;line-height: 1.65rem;
    .red{color:#F00}
    .btn{float:right}
  }
</style>
