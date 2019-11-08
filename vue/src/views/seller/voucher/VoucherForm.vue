<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header :title="(mode=='edit'?'编辑':'新增')+'代金券'" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
      </mt-header>
    </div>
    <mt-field label="代金券名称" type="text" v-model="vouchertemplate.vouchertemplate_title" />
    <div v-if="!seller.is_platform_store" @click="storeClassVisible=true">
      <mt-cell title="店铺分类" is-link :value="store_class_name" />
    </div>
    <mt-field label="有效期" type="date" v-model="endtime" />
    <div @click="priceVisible=true">
      <mt-cell title="面额" is-link :value="price" />
    </div>
    <mt-field label="发放总数" type="number" v-model="vouchertemplate.vouchertemplate_total" />
    <div @click="limitVisible=true">
      <mt-cell title="每人限领" is-link :value="limit_name" />
    </div>
    <mt-field label="消费金额" type="number" v-model="vouchertemplate.vouchertemplate_limit" />
    <mt-field label="描述" type="text" v-model="vouchertemplate.vouchertemplate_desc" />
    <mt-cell title="有效">
      <mt-radio
              class="radio-wrapper"
              v-model="vouchertemplate.vouchertemplate_state"
              :options="state_options">
      </mt-radio>
    </mt-cell>
    <mt-button class="ds-button-large" type="primary" v-on:click="submit">保存</mt-button>
    <!--店铺分类-->
    <mt-popup v-model="storeClassVisible" position="right" class="common-popup-wrapper">
      <div class="common-header-wrap">
        <mt-header title="店铺分类" class="common-header">
          <mt-button slot="left" icon="back" @click="storeClassVisible=false"></mt-button>
        </mt-header>
      </div>
      <div class="common-popup-content">
        <mt-radio
                v-model="store_class_id"
                :options="store_class_options">
        </mt-radio>
      </div>
    </mt-popup>
    <!--面额-->
    <mt-popup v-model="priceVisible" position="right" class="common-popup-wrapper">
      <div class="common-header-wrap">
        <mt-header title="面额" class="common-header">
          <mt-button slot="left" icon="back" @click="priceVisible=false"></mt-button>
        </mt-header>
      </div>
      <div class="common-popup-content">
        <mt-radio
                v-model="price"
                :options="price_options">
        </mt-radio>
      </div>
    </mt-popup>
    <!--限领-->
    <mt-popup v-model="limitVisible" position="right" class="common-popup-wrapper">
      <div class="common-header-wrap">
        <mt-header title="每人限领" class="common-header">
          <mt-button slot="left" icon="back" @click="limitVisible=false"></mt-button>
        </mt-header>
      </div>
      <div class="common-popup-content">
        <mt-radio
                v-model="limit"
                :options="limit_options">
        </mt-radio>
      </div>
    </mt-popup>
  </div>
</template>

<script>
import { getVoucherList, getCommonData, addVoucher, editVoucher, getVoucherInfo } from '../../../api/sellerVoucher'
import { getStoreClass } from '../../../api/seller'
import { Toast, Indicator } from 'mint-ui'
import { mapState, mapMutations } from 'vuex'
export default {
  name: 'SellerVoucherForm',
  components: {
  },
  data () {
    return {
      mode: this.$route.query.mode,
      tid: this.$route.query.tid,
      storeClassVisible: false,
      store_class_options: [{
        label: '所有分类',
        value: '0'
      }],
      vouchertemplate: {},
      store_class_id: '0',
      store_class_list: { '0': '所有分类' },
      store_class_name: '所有分类',
      priceVisible: false,
      price_options: [],
      price: '',
      limitVisible: false,
      limit_options: [{
        label: '不限',
        value: '0'
      }],
      limit: '',
      limit_name: '不限',
      state_options: [{
        label: '是',
        value: '1'
      },
      {
        label: '否',
        value: '2'
      }],
      endtime: ''
    }
  },
  created () {
    getCommonData().then(res => {
      for (var i = 1; i <= res.result.voucher_buyertimes_limit; i++) {
        this.limit_options.push({
          label: i + '张',
          value: i + ''
        })
      }
      let price_options = res.result.pricelist
      if (this.mode != 'edit') {
        this.price = price_options[0].voucherprice
        this.limit = '0'
        this.vouchertemplate.vouchertemplate_state = '1'
      } else {
        getVoucherInfo(this.tid).then(res => {
          this.vouchertemplate = res.result.t_info
          this.vouchertemplate.vouchertemplate_enddate = this.$moment.unix(this.vouchertemplate.vouchertemplate_enddate).format('YYYY-MM-DD')
          this.endtime = this.vouchertemplate.vouchertemplate_enddate
          this.price = this.vouchertemplate.vouchertemplate_price + ''
          this.limit = this.vouchertemplate.vouchertemplate_eachlimit + ''
          this.store_class_id = this.vouchertemplate.vouchertemplate_sc_id + ''
          if (this.store_class_list) {
            this.store_class_name = this.store_class_list[this.store_class_id]
          }
          this.vouchertemplate.vouchertemplate_state = this.vouchertemplate.vouchertemplate_state + ''
        }).catch(function (error) {
          Toast(error.message)
        })
      }
      for (var i in price_options) {
        this.price_options.push({
          label: price_options[i].voucherprice,
          value: price_options[i].voucherprice
        })
      }
    }).catch(function (error) {
      Toast(error.message)
      this.$router.go(-1)
    })
    if (this.seller.is_platform_store) {
      this.vouchertemplate.vouchertemplate_sc_id = this.seller.storeclass_id
    }
    getStoreClass().then(res => {
      let store_class_options = res.result.store_class
      for (var i in store_class_options) {
        this.store_class_list[store_class_options[i].storeclass_id] = store_class_options[i].storeclass_name
        this.store_class_options.push({
          label: store_class_options[i].storeclass_name,
          value: store_class_options[i].storeclass_id + ''
        })
      }
      if (this.mode == 'edit') {
        if (this.vouchertemplate) {
          this.store_class_id = this.vouchertemplate.vouchertemplate_sc_id + ''
          this.store_class_name = this.store_class_list[this.store_class_id]
        }
      }
    }).catch(function (error) {
      Toast(res.message)
    })
  },
  watch: {
    store_class_id: function (store_class_id) {
      this.vouchertemplate.vouchertemplate_sc_id = store_class_id
      this.store_class_name = this.store_class_list[store_class_id]
      this.storeClassVisible = false
    },
    price: function (price) {
      this.vouchertemplate.vouchertemplate_price = price
      this.price = price + ''
      this.priceVisible = false
    },
    limit: function (limit) {
      this.vouchertemplate.vouchertemplate_eachlimit = limit
      this.limit_name = (limit != '0') ? (limit + '张') : '不限'
      this.limitVisible = false
    }
  },
  computed: {
    ...mapState({
      seller: state => state.seller.info
    })

  },
  methods: {
    submit () {
      this.vouchertemplate.vouchertemplate_enddate = this.endtime
      if (this.mode == 'edit') {
        editVoucher(this.tid, this.vouchertemplate.vouchertemplate_title, this.vouchertemplate.vouchertemplate_total, this.vouchertemplate.vouchertemplate_price, this.vouchertemplate.vouchertemplate_limit, this.vouchertemplate.vouchertemplate_desc, this.vouchertemplate.vouchertemplate_enddate, this.vouchertemplate.vouchertemplate_sc_id, this.vouchertemplate.vouchertemplate_eachlimit, this.vouchertemplate.vouchertemplate_state).then(res => {
          Toast(res.message)
          this.$router.push({ name: 'SellerVoucherList' })
        }).catch(function (error) {
          Toast(error.message)
        })
      } else {
        addVoucher(this.vouchertemplate.vouchertemplate_title, this.vouchertemplate.vouchertemplate_total, this.vouchertemplate.vouchertemplate_price, this.vouchertemplate.vouchertemplate_limit, this.vouchertemplate.vouchertemplate_desc, this.vouchertemplate.vouchertemplate_enddate, this.vouchertemplate.vouchertemplate_sc_id, this.vouchertemplate.vouchertemplate_eachlimit).then(res => {
          Toast(res.message)
          this.$router.push({ name: 'SellerVoucherList' })
        }).catch(function (error) {
          Toast(error.message)
        })
      }
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
.radio-wrapper.mint-radiolist {
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
