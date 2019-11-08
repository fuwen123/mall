<!-- footer.vue -->
<template>
	<div
		class="ui-detail-footer show-cart-footer"
		v-if="detailInfo"
	>
		<div class="footer-flex">
			<div class="left">
				<span class="iconfont" v-on:click="goCart()">&#xe6ae;</span>
				<span class="icon" v-if="cartNumber > 0">{{ getCarCount }}</span>
			</div>
			<div class="right">
				<div
					class="button active-cart"
					v-on:click="addShopping(true)"
					v-if="detailInfo.pgoods_storage > 0"
				>
					加入购物车
				</div>
				<div class="button disabled-cart" v-if="detailInfo.pgoods_storage <= 0">
					加入购物车
				</div>
				<div
					class="button active-buy"
					v-on:click="checkout()"
					v-if="detailInfo.pgoods_storage > 0"
				>
					立即购买
				</div>
				<div class="button disabled-buy" v-if="detailInfo.pgoods_storage <= 0">
					立即购买
				</div>
			</div>
		</div>

		<p class="good-stock-none" v-if="detailInfo.pgoods_storage <= 0">
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
import { MessageBox } from 'mint-ui'
import shopping from './child/Shopping'
import { cartQuantity } from '../../../api/memberPointscart'
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
      // 是否显示购物车浮层
      isShowcartInfo: state => state.pointsgoods.isShowcartInfo,
      detailInfo: state => state.pointsgoods.detailInfo,
      isOnline: state => state.member.isOnline,
      number: state => state.pointsgoods.number
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
      changeType: 'changeType'
    }),
	  getCartCount () {
		  cartQuantity().then(res => {
			  if (res) {
				  this.cartNumber = res.result.cart_count
			  }
		  })
	  },
    // 加入购物车
    addShopping (value) {
      this.saveCartState(value)
      this.changeType('确定')
    },

    // 立即购买
    checkout () {
      this.$router.push({ name: 'MemberPointsBuyStep1', query: { buy_now: 1, cart_id: this.detailInfo.pgoods_id + '|1' } })
    },

    // 购物车
    goCart () {
      if (this.isOnline) {
        this.$router.push({ name: 'MemberPointsCart', params: { type: 0 } })
      } else {
        this.$router.push({ name: 'HomeMemberLogin' })
      }
    },



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
		line-height: 1rem;
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
		.iconfont {
			width: 1.5rem;
			height: 1.5rem;
			flex-shrink: 0;
			margin: 0.35rem 1.25rem;
			font-size:1.5rem;
		}
		span.icon {
			position: absolute;
			left: 2rem;
			top: 0.4rem;
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
			font-size: 0.8rem;
			color: #ffffff;
			text-align: center;
			line-height: 2.2rem;
			cursor: pointer;
		}
		.disabled-cart {
			background: #c3c3c3;
		}
		.active-cart {
			background: $primaryColor;
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
