<template>
<div class="container">
    <div v-for="(store,index) in storeCartList">
        <div class="store">
            <i class="iconfont">&#xe62d;</i>
            <span>{{store.store_name}}</span>
        </div>
        <ul class="cart-item">
            <li class="buy-item" v-for="goods in store.goods_list" @click="$router.push({name:'HomeGoodsdetail',query:{goods_id:goods.goods_id}})">
                <div class="goods-pic"><img :src="goods.goods_image_url"/></div>
                <div class="goods-info">
                    <dl>
                        <dt class="goods-name">
                            <a>
                                <i class="promotion-icon" v-if="goods.xianshi_info">{{goods.xianshi_info.xianshi_title}}</i>
                                <i class="promotion-icon" v-if="goods.ifgroupbuy">抢购</i>
                                <i class="promotion-icon" v-if="goods.ifmgdiscount">会员折扣</i>
                                <i class="promotion-icon" v-if="goods.ifpintuan">拼团</i>
                                <i class="promotion-icon" v-if="goods.ifbargain">砍价</i>
                                <i class="promotion-icon" v-if="goods.bl_id">优惠套装</i>
                                {{goods.goods_name}}
                            </a>
                        </dt>
                        <dd class="goods-subtotal">
                            <span class="goods-price">{{goods.goods_price}}</span>
                            <span class="goods-count">×{{goods.goods_num}}</span>
                        </dd>
                    </dl>
                </div>

            </li>
            <li class="buy-item"  v-if="store.store_mansong_rule_list" @click="$router.push({name:'HomeGoodsdetail',query:{goods_id:store.store_mansong_rule_list.goods_id}})">
                <div class="goods-pic"><img :src="store.store_mansong_rule_list.goods_image_url"/></div>
                    <div class="goods-info">
                        <dl>
                            <dt class="goods-name">
                                <a>
                                    <i class="promotion-icon">赠品</i>
                                    {{store.store_mansong_rule_list.mansong_goods_name}}
                                </a>
                            </dt>
                            <dd class="goods-subtotal">
                                <span class="goods-price">0.00</span>
                                <span class="goods-count">×1</span>
                            </dd>
                        </dl>
                    </div>

            </li>
        </ul>
        <div class="cart-subtotal">
            <div v-if="addressApi.allow_offpay==1" @click="togglePay=true"><mt-cell class="menu-item" title="付款方式" ><span slot="icon" class="left-icon iconfont">&#xe6f2;</span>{{pay_name}}<i class="indicator iconfont">&#xe650;</i></mt-cell></div>
            <div v-if="store.store_voucher_list.length" @click="toggleVoucher(store.store_id)"><mt-cell class="menu-item" title="代金券" ><span slot="icon" class="left-icon iconfont">&#xe6f2;</span>{{myVoucher[store.store_id]}}<i class="indicator iconfont">&#xe650;</i></mt-cell></div>
            <div class="message">
                <mt-field label="备注" placeholder="店铺订单留言:" type="textarea" v-model="message[store.store_id]" @change="changeMessage(store.store_id)"></mt-field>
            </div>
            <checkout-desc class="desc-item mt-5" title="商品金额" :subtitle="store.store_goods_total"></checkout-desc>
            <checkout-desc class="desc-item" title="优惠" v-if="store.store_mansong_rule_list" :subtitle="store.store_mansong_rule_list.discount" :discount="true"></checkout-desc>
            <checkout-desc class="desc-item" title="运费" :subtitle="addressApi.content[store.store_id]"></checkout-desc>
            <checkout-desc class="desc-item" title="本店合计" :subtitle="totalAmount[store.store_id]"></checkout-desc>
        </div>
        <mt-popup v-model="togglePay" position="right" class="common-popup-wrapper">
            <div class="common-header-wrap">
                <mt-header title="付款方式" class="common-header">
                    <mt-button slot="left" icon="back" @click="togglePay=false"></mt-button>
                </mt-header>
            </div>
            <div class="common-popup-content">
                <mt-radio
                        v-model="pay_code"
                        :options="pay_options">
                </mt-radio>
            </div>
        </mt-popup>
        <mt-popup v-model="voucherVisible[store.store_id]" position="right" class="common-popup-wrapper">
            <div class="common-header-wrap">
                <mt-header title="代金券" class="common-header">
                    <mt-button slot="left" icon="back" @click="toggleVoucher(store.store_id)"></mt-button>
                    <mt-button slot="right" @click="selectVoucher('',store.store_id)">不使用</mt-button>
                </mt-header>
            </div>
            <div class="common-popup-content">
                <div class="common-voucher common-voucher02" :class="(item.voucher_state != 1)?'disable':''" v-for="(item,index) in store.store_voucher_list" :key="item.voucher_id" @click="selectVoucher(item,store.store_id)">
                    <div class="par"><p>订单满{{item.voucher_limit}}元</p><sub class="sign">￥</sub><span>{{item.voucher_price}}</span></div>
                    <div class="copy">有效期至<p><br>{{$moment.unix(item.voucher_enddate).format('YYYY.MM.DD')}}</p></div>
                    <i></i>
                </div>
            </div>
        </mt-popup>
    </div>
</div>
</template>

<script>
import CheckoutDesc from './CheckoutDesc'
export default {
  name: 'CheckoutStore',
  data () {
    return {
      pay_name: '在线付款',
      pay_code: 'online',
      pay_options: [{
        label: '在线付款',
        value: 'online'
      }, {
        label: '货到付款',
        value: 'offline'
      }],
      pay_list: { online: '在线付款', offline: '货到付款' },
      togglePay: false,
      totalAmount: {},
      myVoucher: [],
      voucherVisible: [],
      message: []
    }
  },
  components: {
    CheckoutDesc
  },
  props: ['storeCartList', 'addressApi','finalTotalList'],
  created: function () {
    for (var store_id in this.storeCartList) {
      this.calcOrder(store_id)
    }
  },
  watch: {
    pay_code: function (pay_code) {
      this.pay_name = this.pay_list[pay_code]
      this.togglePay = false
      this.$emit('selectPay', pay_code)
    }
  },
  methods: {
    changeMessage (store_id) {
      this.$emit('changeMessage', this.message[store_id], store_id)
    },
    selectVoucher (voucherInfo,store_id) {
      this.toggleVoucher(store_id)
      this.$emit('selectVoucher', voucherInfo,store_id)
      this.storeCartList[store_id].store_voucher_info = voucherInfo
      this.calcOrder(store_id)
    },
    calcOrder (store_id) {
      let price = parseFloat(this.finalTotalList[store_id])
      if (this.storeCartList[store_id].store_voucher_list.length) {
        if(this.storeCartList[store_id].store_voucher_info){
          price -= parseFloat(this.storeCartList[store_id].store_voucher_info.voucher_price)
          if (typeof (this.myVoucher[store_id]) === 'undefined') {
            this.myVoucher[store_id] = ''
          }
          this.myVoucher.splice(store_id, 1, this.storeCartList[store_id].store_voucher_info.voucher_price + '元')
        }else{
          this.myVoucher.splice(store_id, 1, '不使用')
        }
      }
      if (price < 0) {
        price = 0
      }
      this.totalAmount[store_id] = price
    },
    toggleVoucher (store_id) {
      if (typeof (this.voucherVisible[store_id]) === 'undefined') {
        this.voucherVisible[store_id] = false
      }
      this.voucherVisible.splice(store_id, 1, !this.voucherVisible[store_id])
    }
  }
}
</script>

<style scoped lang="scss">
.store{    position: relative;
    z-index: 1;
    display: block;
    height: 0.9rem;
    padding: 0.5rem;
    font-size: 0.7rem;
    line-height: 0.9rem;
    border-bottom: solid 0.05rem #EEE;
}
.store i{
        display: inline-block;
        width: 0.8rem;
        height: 0.9rem;
        margin-right: 0.2rem;
        vertical-align: middle;
    }
    .cart-item{background-color: #FFF;
        border-bottom: solid 0.05rem #EEE;}
    .buy-item{    display: block;
        position: relative;
        z-index: 1;
        margin-left: 0.5rem;
        padding: 0.5rem 0;}
    .buy-item .goods-pic{display: block;
        width: 2.7rem;
        height: 2.7rem;
        padding: 0.2rem;
        position: absolute;
        z-index: 1;
        top: 0.5rem;
        left: 1.1rem;
        border: solid 0.05rem #EEE;
        border-radius: 0.2rem;}
.buy-item .goods-pic img{width: 2.7rem;
    height: 2.7rem;}
.buy-item .goods-info{    display: block;
        vertical-align: top;
        height: 3.1rem;
        margin: 0 2rem 0 4.5rem;
        position: relative;
        z-index: 1;}
.buy-item .goods-info .goods-name a {
    display: block;
    height: 2rem;
    font-size: 0.6rem;
    color: #111;
    line-height: 1rem;
    overflow: hidden;
}
.buy-item .goods-info  .goods-type {
    overflow: hidden;
    white-space: nowrap;
    width: 70%;
    height: 0.9rem;
    font-size: 0.45rem;
    line-height: 0.9rem;
    color: #999;
    text-overflow: ellipsis;
}
.buy-item .goods-info  .goods-subtotal {
    display: block;
    height: 1rem;
    margin: 0 0.5rem 0 4.5rem;
    line-height: 1rem;
    font-size: 0.6rem;
    position: relative;
    z-index: 1;
}
.buy-item .goods-info  .goods-subtotal .goods-price {
    color: #DB4453;
    font-size: 0.55rem;
    font-weight: 600;
}
.buy-item .goods-info  .goods-subtotal  .goods-count {
    font-size: 0.5rem;
    line-height: 1rem;
    position: absolute;
    z-index: 1;
    top: 0rem;
    right: 1rem;
}
 .cart-subtotal{}
.cart-subtotal dl {
    position: relative;
    z-index: 1;
    height: 1rem;
    padding: 0.4rem 0 0;
    font-size: 0;
}
.cart-subtotal dt {
    position: absolute;
    z-index: 1;
    top: 0.4rem;
    left: 0.4rem;
    display: block;
    width: 50%;
    height: 1rem;
    font-size: 0.6rem;
    line-height: 1rem;
}
.cart-subtotal dd {
    height: 1rem;
    margin: 0 0.5rem 0 50%;
    text-align: right;
    font-size: 0.6rem;
    line-height: 1rem;
}
.cart-subtotal .message {
    position: relative;
    z-index: 1;
    display: block;
    padding-top: 0.4rem;
}
.desc-item {
    height: 1.5rem;
}
.common-voucher{margin:0.5rem auto}
    .promotion-icon{display: inline-block;line-height: .7rem;border: 1px solid $primaryColor;border-radius: .1rem;padding:.2rem;font-size: .6rem;color: $primaryColor}
</style>
