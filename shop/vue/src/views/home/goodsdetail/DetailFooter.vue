<!-- footer.vue -->
<template>
	<div
		class="ui-detail-footer"
		v-if="detailInfo"
		v-bind:class="{
			'hidden-cart-footer': ispromotion,
			'show-cart-footer': !ispromotion
		}"
	>
		<div class="footer-flex">
			<div class="left">
				<div class="item" v-on:click="$router.push({name:'HomeStoredetail',query:{id:storeInfo.store_id}})">
					<span class="iconfont">&#xe69f;</span>
					<span class="text">店铺</span>
				</div>
				<div class="item" v-if="config && config.node_site_use == '1' && config.node_site_url" v-on:click="goChat()">
					<span class="iconfont">&#xe6f1;</span>
					<span class="text">客服</span>
				</div>
				<div class="item" v-on:click="goCart()">
					<span class="iconfont">&#xe6ae;</span>
					<span class="icon" v-if="cartNumber > 0">{{ getCarCount }}</span>
					<span class="text">购物车</span>
				</div>

			</div>
			<div class="right">
				<div
					class="button active-cart"
					v-on:click="addShopping(true)"
					v-if="!detailInfo.bargain_info && ((!detailInfo.pintuan_type && detailInfo.goods_storage > 0 && detailInfo.cart) || (detailInfo.pintuan_type && detailInfo.pintuangroup_list.length))"
				>
					{{detailInfo.pintuan_type?'参加拼团':'加入购物车'}}
				</div>
				<div class="button disabled-cart" v-if="!detailInfo.bargain_info && ((!detailInfo.pintuan_type && detailInfo.goods_storage <= 0 && detailInfo.cart) || (detailInfo.pintuan_type && detailInfo.goods_storage <= 0 && detailInfo.pintuangroup_list.length))">
					{{detailInfo.pintuan_type?'参加拼团':'加入购物车'}}
				</div>
				<div
					class="button active-buy"
					v-on:click="checkout()"
					v-if="detailInfo.goods_storage > 0"
				>
					{{detailInfo.bargain_info?(detailInfo.bargainorder_info?(detailInfo.bargainorder_info.bargainorder_state==2?'砍价成功去下单':'正在砍价'):'发起砍价'):(detailInfo.pintuan_type?'立刻开团':(detailInfo.is_presell==1?'预售购买（'+$moment.unix(detailInfo.presell_deliverdate).format('YYYY年MM月DD日')+'发货）':(detailInfo.is_goodsfcode==1?'F码购买':'立即购买')))}}
				</div>
				<div class="button disabled-buy" v-if="detailInfo.goods_storage <= 0">
					{{detailInfo.bargain_info?(detailInfo.bargainorder_info?(detailInfo.bargainorder_info.bargainorder_state==2?'砍价成功去下单':'正在砍价'):'发起砍价'):(detailInfo.pintuan_type?'立刻开团':(detailInfo.is_presell==1?'预售购买（'+$moment.unix(detailInfo.presell_deliverdate).format('YYYY年MM月DD日')+'发货）':(detailInfo.is_goodsfcode==1?'F码购买':'立即购买')))}}
				</div>
			</div>
		</div>

		<p class="good-stock-none" v-if="detailInfo.goods_storage <= 0">
			所选产品暂时无货，非常抱歉！
		</p>

		<shopping v-if="isShowcartInfo" :isShowcartInfo="isShowcartInfo"></shopping>

		<!-- 加入购物车显示动画 -->
		<div class="ui-cart-animation" v-if="isAnimation">
			<mt-spinner type="snake" color="#e93b3d"></mt-spinner>
		</div>
	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import { Toast } from 'mint-ui'
import shopping from './child/Shopping'
import { cartQuantity } from '../../../api/homecart'
import { addBargain } from '../../../api/memberBargain'
export default {
  data () {
    return {
      cartNumber: 0,
      isAnimation: false // 加入购物车成功之后是否显示动画
    }
  },

  components: {
    shopping
  },

  props: {
    ishidefooter: {
      type: Boolean,
      default: false
    }
  },

  computed: {
    ...mapState({
      config: state => state.config.config,
      user: state => state.member.info,
      // 是否显示购物车浮层
      isShowcartInfo: state => state.goodsdetail.isShowcartInfo,
      storeInfo: state => state.goodsdetail.storeInfo,
      detailInfo: state => state.goodsdetail.detailInfo,
      isOnline: state => state.member.isOnline,
      ispromotion: state => state.goodsdetail.ispromotion,
      // cartNumber: state => state.tabBar.cartNumber,
      chooseinfo: state => state.goodsdetail.chooseinfo,
      number: state => state.goodsdetail.number
    }),
    getCarCount () {
      if (this.cartNumber > 0 && this.cartNumber < 100) {
        return this.cartNumber
      } else if (this.cartNumber >= 100) {
        return '99+'
      }
    }
  },

  created () {
    this.$on('start-addcart-animation', () => {
      this.isAnimation = true
    })
    this.$on('end-addcart-animation', () => {
      this.isAnimation = false
      this.saveCartState(false)
    })
	  this.getCartCount()
	  this.$on('update-cart-num', () => {
		  this.getCartCount()
	  })
  },

  methods: {
    ...mapMutations({
      saveCartState: 'saveCartState',
      changeType: 'changeType',
      saveSelectedCartGoods: 'saveSelectedCartGoods'
    }),
	  goChat () {

		  	if(!this.user){
          Toast('请先登录')
        }else if (this.user.member_id == this.storeInfo.member_id) {
				  Toast('不能和自己聊天')
			  } else {
				  this.$router.push({ name: 'MemberChatInfo', query: { t_id: this.storeInfo.member_id, goods_id: this.detailInfo.goods_id } })
			  }
		 
	  },
	  getCartCount () {
		  if (this.isOnline) {
			  cartQuantity().then(res => {
				  if (res) {
					  this.cartNumber = res.result.cart_count
				  }
			  })
		  }
	  },
    // 加入购物车
    addShopping (value) {
      this.saveCartState(value)
      this.changeType('确定')
    },

    // 立即购买
    checkout () {
      if (this.detailInfo.is_virtual == '1') {
      let params = { goods_id: this.detailInfo.goods_id, quantity: 1 }
      if (this.detailInfo.pintuan_type) {
          params['pintuan_id'] = this.detailInfo.pintuan_id
          params['pintuangroup_id'] = 0
        }
		  this.$router.push({ name: 'MemberVrBuyStep1', query:  params})
      } else {
      	let params = { buy_now: 1, cart_id: this.detailInfo.goods_id + '|1' }
      	if (this.detailInfo.bargain_info) {
          if (this.detailInfo.bargainorder_info) {
            if (this.detailInfo.bargainorder_info.bargainorder_state == 2) {
              params['bargainorder_id'] = this.detailInfo.bargainorder_info.bargainorder_id
            } else {
              this.$router.push({ name: 'HomeBargainshare', query: { bargainorder_id: this.detailInfo.bargainorder_info.bargainorder_id } })
              return
            }
          } else {
            // 新增砍价
			  addBargain(this.detailInfo.bargain_info.bargain_id).then(res => {
			  	this.$router.push({ name: 'HomeBargainshare', query: { bargainorder_id: res.result.bargainorder_id } })
			  }).catch(function (error) {
				  Toast(error.message)
			  })
			  return
          }
        } else if (this.detailInfo.pintuan_type) {
          params['pintuan_id'] = this.detailInfo.pintuan_id
          params['pintuangroup_id'] = 0
        }
		  this.$router.push({ name: 'MemberBuyStep1', query: params })
      }
    },

    // 购物车
    goCart () {
      if (this.isOnline) {
        this.$router.push({ name: 'HomeCart', params: { type: 0 } })
      } else {
        this.$router.push({ name: 'HomeMemberLogin' })
      }
    },

    /*
		 * fromatArray: 格式化数组
		 */
    fromatArray (delimiter, arrays) {
      let data = ''
      if (delimiter) {
        data = arrays.join(delimiter)
      }
      return delimiter ? data : arrays
    },

    /*
		 * isHasStock: 是否还有库存
		 */
    isHasStock (id) {
      let data = this.detailInfo.stock
      if (data.length > 0) {
        for (let i = 0, len = data.length; i <= len - 1; i++) {
          if (data[i].goods_attr == id) {
            return '' + data[i].stock_number + ''
          }
        }
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.ui-detail-footer {
	background: rgba(255, 255, 255, 1);
	border-top: 0.5px solid #e8eaed;
	width: auto;
	position: absolute;
	bottom: 0;
	left: 0;
	right: 0;
	z-index: 0;
	&.hidden-cart-footer {
		display: none;
	}

	&.show-cart-footer {
		display: block;
	}

	.footer-flex {
		display: flex;
		justify-content: space-between;
		align-content: center;
		align-items: center;
		height: 2.2rem;
	}
	p.good-stock-none {
		width: 100%;
		height: 1.6rem;
		background: #c3c3c3;
		opacity: 0.5;
		font-size: 0.7rem;
		color: #333;
		position: absolute;
		text-align: center;
		line-height: 1.6rem;
		padding: 0;
		margin: 0;
		bottom: 2.2rem;
	}
	div.left {
		display: flex;
		justify-content: center;
		align-items: center;
		width: 5.5rem;
		.item{flex:1;text-align: center;line-height: 1;position:relative;
			.iconfont {
				flex-shrink: 0;
				font-size:1.2rem;

			}
			.text{font-size:.5rem;color:gray;display: block}
		}
		span.icon {
			position: absolute;
			right: 0;
			top: 0;
			font-size: 0.5rem;
			line-height: 0.7rem;
			width: 0.9rem;
			height: 0.7rem;
			background: #ef3338;
			border-radius: 1rem;
			text-align: center;
			color: #ffffff;
		}
	}
	div.right {
		flex: 1;
		display: flex;
		flex-direction: row;
		.button {
			flex: 1;
			height: 2.2rem;
			font-size: 0.7rem;
			color: #ffffff;
			text-align: center;
			line-height: 2.2rem;
			cursor: pointer;
		}
		.disabled-cart {
			background: #c3c3c3;
		}
		.active-cart {
			background: $mainColor;
		}
		.disabled-buy {
			background: #999999;
		}
		.active-buy {
			background: #000;
		}
	}
}
.ui-cart-animation {
	position: fixed;
	top: 50%;
	left: 50%;
}
</style>
