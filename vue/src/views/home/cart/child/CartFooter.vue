<!-- CartFooter.vue -->
<template>
	<div class="ui-cart-footer" v-bind:class="{ 'has-bottom': issShowTabbar }">
		<div class="list-checkbox">
			<input
				type="checkbox"
				class="checkbox"
				id="checkbox-all"
				v-model="isSelected"
				@change="selectedAll(isSelected)"
			/>
			<label for="checkbox-all"><span class="iconfont">&#xe69b;</span></label>
			<i v-if="isCheckedAll">全选</i>
			<i v-if="!isCheckedAll" class="total-price"
			>合计<span>￥ {{ totalPrice }} </span></i
			>
		</div>
		<span
			class="cart-footer-btn remove"
			v-if="isCheckedAll"
			@click="deleteSelected()"
			>删除</span
		>
		<span
			class="cart-footer-btn checkout"
			v-if="!isCheckedAll"
			@click="checkout"
			>结算({{ totalAmount }})</span
		>
	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import { Toast } from 'mint-ui'
export default {
  data () {
    return {
      isSelected: true,
      deleteGoods: []
    }
  },
  computed: mapState({
  }),

  props: {
    totalPrice: {},
    totalAmount: {},
    cartId: {},
    isCheckedAll: {
      type: Boolean,
      default: false
    },
    issShowTabbar: {
      type: Number,
      default: 0
    },
    isStatus: {
      type: Boolean,
      default: true
    }
  },

  watch: {
    isCheckedAll: function (value) {
      this.isSelected = !value
    },

    isStatus: function (value) {
      this.isSelected = value
    }
  },

  methods: {
    /*
		 *  selectedAll: 底部全选按钮的状态
		 *  @param: value 底部全选按钮的值
		 */
    selectedAll (value) {
      this.$parent.$emit('cart-bottom-status', { isCheckAll: value })
    },

    /*
		 *  deleteSelected: 删除购物车商品
		 */
    deleteSelected () {
      this.$parent.$emit('update-cart-list', { isdelete: true })
    },
    /*
		 *  checkout: 结算
		 */
    checkout () {
    	if (!this.isCheckedAll && this.totalAmount != 0) {
        this.$router.push({ 'name': 'MemberBuyStep1', 'query': { 'cart_id': this.cartId } })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.ui-cart-footer {
	position: relative;
	display: flex;
	justify-content: space-between;
	align-content: center;
	align-items: center;
	height: 2.2rem;
	background: rgba(255, 255, 255, 1);
	border-bottom: 1px solid #e8eaed;
	padding-left: 0.6rem;
	bottom: 0;
	position: fixed;
	width: -webkit-fill-available;
	.list-checkbox {
		flex-shrink: 0;
		position: relative;
		margin-right:0.25rem;
		height: 2.2rem;
		line-height: 2.2rem;
		label {
			position: absolute;
			top: 0.6rem;
			left: 0;
			width: 1rem;
			height: 1rem;
			display: inline-block;
			border-radius:50%;
			border:1px solid #333;
			box-sizing:border-box;
			.iconfont{display: none;line-height: 1rem;text-align: center;}
		}
		input {
			position: relative;
			margin: 0;
			z-index: -999;
			background-color: #fff;
			opacity: 0;
			&:checked + label {
				border-color:$primaryColor;
				background-color:$primaryColor;
				.iconfont{display: block;color:#fff}
			}
			&:focus {
				outline-offset: 0;
			}
		}
		i {
			padding-left: 0.6rem;
			font-style: normal;
			font-size: 0.7rem;
			color: rgba(41, 43, 45, 1);
			&.total-price span {
				color: $primaryColor;
			}
		}
	}
	span.cart-footer-btn {
		width: 7.5rem;
		height: 2.3rem;
		display: inline-block;
		font-size: 0.8rem;
		color: rgba(255, 255, 255, 1);
		line-height: 2.25rem;
		text-align: center;
		cursor: pointer;
		font-weight: normal;
	}
	.checkout {
		background: $primaryColor;
	}
	.disable {
		background: #c3c3c3;
	}
	.remove {
		background: #f23030;
	}
}
.has-bottom {
	bottom: 2.5rem;
}
</style>
