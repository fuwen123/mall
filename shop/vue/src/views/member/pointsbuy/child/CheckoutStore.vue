<template>
<div>
    <div>

        <ul class="cart-item">
            <li class="buy-item" v-for="goods in storeCartList">
                <div class="goods-pic"><img :src="goods.pgoods_image"/></div>
                <div class="goods-info">
                    <dl>
                        <dt class="goods-name"><a>{{goods.pgoods_name}}</a></dt>
                        <dd class="goods-type"></dd>
                        <dd class="goods-subtotal">
                            <span class="goods-price">{{goods.pgoods_points}}</span>
                            <span class="goods-count">×{{goods.quantity}}</span>
                        </dd>
                    </dl>
                </div>
            </li>
        </ul>
        <div class="cart-subtotal">

            <div class="message">
                <mt-field label="备注" placeholder="订单留言:" type="textarea" v-model="message"></mt-field>
            </div>

            <checkout-desc class="desc-item" title="本店合计" :subtitle="totalAmount"></checkout-desc>
        </div>

    </div>
</div>
</template>

<script>
import CheckoutDesc from './CheckoutDesc'
export default {
  name: 'CheckoutStore',
  data () {
    return {

      message: ''
    }
  },
  components: {
    CheckoutDesc
  },
  props: ['storeCartList', 'totalAmount'],
  created: function () {

  },
  methods: {
    changeMessage (store_id) {
      this.$emit('changeMessage', this.message)
    }
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
    height:1.5rem;
}
.common-voucher{margin:0.5rem auto}
</style>
