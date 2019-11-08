<template>
<div>
    <div>
        <div class="store">
            <i class="iconfont">&#xe62d;</i>
            <span>{{storeCartList.store_name}}</span>
        </div>
        <ul class="cart-item">
            <li class="buy-item">
                <div class="goods-pic"><img :src="storeCartList.goods_image_url"/></div>
                <div class="goods-info">
                    <dl>
                        <dt class="goods-name"><a>{{storeCartList.goods_name}}</a></dt>
                        <dd class="goods-type"></dd>
                        <dd class="goods-subtotal">
                            <span class="goods-price">{{storeCartList.goods_price}}</span>
                            <span class="goods-count">×{{storeCartList.quantity}}</span>
                        </dd>
                    </dl>
                </div>
            </li>
        </ul>
        <div class="cart-subtotal">
            <div class="message">
                <mt-field label="备注" placeholder="店铺订单留言:" type="textarea" v-model="message" @change="changeMessage()"></mt-field>
            </div>
            <div v-if="storeCartList.store_voucher_list.length" @click="voucherVisible=true"><mt-cell class="menu-item" title="代金券" ><span slot="icon" class="left-icon iconfont">&#xe6f2;</span>{{myVoucher}}<i class="indicator iconfont">&#xe650;</i></mt-cell></div>
            <checkout-desc class="desc-item" title="本店合计" :subtitle="totalAmount"></checkout-desc>
        </div>
    <mt-popup v-model="voucherVisible" position="right" class="common-popup-wrapper">
            <div class="common-header-wrap">
                <mt-header title="代金券" class="common-header">
                    <mt-button slot="left" icon="back" @click="voucherVisible=false"></mt-button>
                    <mt-button slot="right" @click="selectVoucher('')">不使用</mt-button>
                </mt-header>
            </div>
            <div class="common-popup-content">
                <div class="common-voucher common-voucher02" :class="(item.voucher_state != 1)?'disable':''" v-for="(item,index) in storeCartList.store_voucher_list" :key="item.voucher_id" @click="selectVoucher(item)">
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
      totalAmount: '',
      voucherVisible: [],
      message:  '',
      myVoucher: '',
      voucherVisible: false,
    }
  },
  components: {
    CheckoutDesc
  },
  props: ['storeCartList'],
  created: function () {
    this.calcOrder()
  },
  methods: {
    changeMessage () {
      this.$emit('changeMessage', this.message)
    },
    selectVoucher (voucherInfo) {
      this.voucherVisible=false
      this.$emit('selectVoucher', voucherInfo)
      this.storeCartList.store_voucher_info = voucherInfo
      this.calcOrder()
    },
    calcOrder () {
      let price = parseFloat(this.storeCartList.goods_total)
      if (this.storeCartList.store_voucher_list.length) {
        if(this.storeCartList.store_voucher_info){
          price -= parseFloat(this.storeCartList.store_voucher_info.voucher_price)

          this.myVoucher=this.storeCartList.store_voucher_info.voucher_price + '元'
        }else{
          this.myVoucher='不使用'
        }
      }
      if (price < 0) {
        price = 0
      }
      this.totalAmount = price
    },
   
  }
}
</script>

<style scoped>
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
    height: 1.4rem;
    font-size: 0.6rem;
    color: #111;
    line-height: 0.7rem;
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
</style>
