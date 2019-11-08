<template>
	<div class="cart-list-wrapper">
		<div v-for="(cartItem, i) in cartList" :key="i">
			<div class="store-info">
				<div class="list-checkbox">
				<input
						type="checkbox"
						class="checkbox"
						:id="'store'+cartItem.store_id"
						v-model="cartItem.checked"
						@change="changeSingleStatu2(i)"
						:disabled="!isCheckedAll && cartItem.disabled"
				/>
				<label :for="'store'+cartItem.store_id"><span class="iconfont">&#xe69b;</span></label>
			</div>
				<span class="store-name">{{cartItem.store_name}}</span>
				<span v-if="cartItem.voucher_list && cartItem.voucher_list.length" class="voucher" @click="goVoucher(i)">领券</span>
			</div>
		<div class="list" v-for="(item, index) in cartItem.goods" :key="index">
			<div class="list-checkbox">
				<input
					type="checkbox"
					class="checkbox"
					:id="'store'+cartItem.store_id+'goods'+index"
					v-model="item.checked"
					@change="changeSingleStatu()"
					:disabled="!isCheckedAll && item.goods_storage == 0"
				/>

				<label :for="'store'+cartItem.store_id+'goods'+index"><span class="iconfont">&#xe69b;</span></label>
			</div>
			<div class="list-item" @click="goDetail(item.goods_id)">
				<div class="item">
					<div class="ui-image">
						<img :src="item.goods_image_url" />
						<span v-if="item.goods_storage == 0" class="stock-info"
							>已售罄</span
						>
						<span
							v-if="
								item.goods_storage > 0 && item.goods_storage <= 10
							"
							class="stock-info"
							>仅剩{{ item.goods_storage }}件</span
						>
						<span
							class="promos"
							v-if="item.xianshi_info"
							>促销</span
						>
					</div>
					<div class="list-info">
						<div class="product-header">
							<h3
								class="product-title"
								v-bind:class="{ 'disabled-list': item.goods_storage == 0 }"
							>
								{{ item.goods_name }}
							</h3>
						</div>
						<h3 class="property-info"></h3>
						<div class="info-price">
							<p
								v-bind:class="{ 'disabled-list': item.goods_storage == 0 }"
							>
								￥{{ item.goods_price }}
							</p>
							<div class="ui-number">
								<div
									class="reduce ui-common"
									@click.stop="reduceNumber(item.cart_id, item.goods_num,i,index)"
									v-bind:class="{ 'reduce-opacity': item.goods_num <= 1 }"
								>
									-
								</div>
								<input
									type="number"
									min="1"
									class="number"
									value="1"
									v-model="item.goods_num"
									readonly="true"
								/>
								<div
									class="add ui-common"
									@click.stop="
										addNumber(
											item.cart_id,
											item.goods_num,
											i,
											index
										)
									"
								>
									+
								</div>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import { Toast, Indicator } from 'mint-ui'
import {
  cartGet,
  cartDelete,
  cartUpdate,
  cartQuantity
} from '../../../../api/homecart'

export default {
  props: {
    isCheckedAll: {
      type: Boolean,
      default: false
    }
  },
  data () {
    return {
      cartList: [], // 购物车列表
      indicator: { spinnerType: 'fading-circle' },
      totalPrice: 0, // 购物车总价
      cartId: '', // 购物车中选中的商品
      totalAmount: 0, // 购物车数量
      promosIds: [] // 促销信息IDS
    }
  },
  created () {
    this.getCartList(true)
  },
  methods: {
    ...mapMutations({
      getAmount: 'calculationAmount',
      getPrice: 'calculationPrice',
      setCartNumber: 'setCartNumber',
      saveSelectedCartGoods: 'saveSelectedCartGoods',
      saveVoucher: 'saveVoucher'
    }),

    /*
		 * getCartList: 获取购物车列表
		 */
    getCartList (value) {
      cartGet().then(res => {
        if (res && res.result.cart_val.length > 0) {
          this.cartList = Object.assign([], res.result.cart_val)
          this.addChecked(value)
          this.renderCart()
        } else {
          this.cartList = []
          this.getAmount(0)
          this.getPrice(0.0)
        }
        this.$parent.$emit('list-is-empty', this.cartList)
      })
    },

    /*
		 * addChecked: 为每个商品添加checked 属性
		 * @param: isSelectedall 是否选中商品 Boolean
		 */
    addChecked (isSelectedall) {
      let temp = this.cartList
      for (var j in temp) {
        let list = temp[j].goods
		  let k = 0
        for (var i in list) {
          if (list[i].goods_storage == 0 && !this.isCheckedAll) {
            list[i].checked = false
			  k++
          } else {
            list[i].checked = isSelectedall
          }
        }
		  temp[j].checked = (k == list.length) ? false : isSelectedall
      }
      this.cartList = Object.assign([], temp)
    },

    /*
		 *  renderCart: 修改商品数量和点击是否选中后 重新计算商品价格和数量
		 */
    renderCart () {
      let temp = this.cartList
      this.promosIds = []
      let cartGoods = []
      let totalAmount = 0
      let totalPrice = 0
      for (var j in temp) {
        let data = temp[j].goods
        for (var i in data) {
          if (data[i].checked) {
            totalAmount += parseInt(data[i].goods_num)
            totalPrice += parseInt(data[i].goods_num) * parseFloat(data[i].goods_price)
            cartGoods.push(data[i].cart_id + '|' + data[i].goods_num)
          }
        }
      }
      this.cartId = cartGoods.toString()
      this.totalPrice = totalPrice.toFixed(2)
      this.totalAmount = totalAmount
      this.$parent.$emit('calcu-cart-data', { totalPrice: this.totalPrice, totalAmount: this.totalAmount, cartId: this.cartId })
    },

    /*
		 * deleteSelected: 删除购物车数据
		 */
    deleteSelected () {
      let temp = this.cartList
      let deleteGoods = []
      this.promosIds = []
      for (var j in temp) {
        let data = temp[j].goods
        for (var i in data) {
          if (data[i].checked) {
            deleteGoods.push(data[i].cart_id)
          }
        }
      }
      if (deleteGoods.length > 0) {
        deleteGoods = deleteGoods.toString()
      } else {
        Toast('当前没有可删除的商品')
        return
      }
      Indicator.open()
      cartDelete(deleteGoods).then(res => {
        if (res) {
          this.getCartList(false)
          Indicator.close()
        }
      })
    },

    /*
		 *  changeSingleStatu: 改变单个商品是否选中的状态, 然后重新获取商品的件数和价格
		 */
    changeSingleStatu () {
      let data = this.cartList
      let length = 0
      let totalLength = 0
      let status = false
      for (var j in data) {
      	let list = data[j].goods
		  let k = 0
		  for (var i in list) {
			  if (list[i].checked) {
				  length = length + 1
				  k++
			  }
		  }
		  if (k == list.length) {
			  data[j].checked = true
		  } else {
			  data[j].checked = false
		  }
		  totalLength += list.length
	  }
      if (length == totalLength) {
        status = true
      } else {
        status = false
      }
      this.$parent.$emit('change-footer-status', status)
      if (!this.isCheckedAll) {
        this.renderCart()
      }
      this.cartList = Object.assign([], data)
    },
	  changeSingleStatu2 (j) {
		  let data = this.cartList
		  let length = 0
		  let totalLength = 0
		  let status = false
		  
			  let list = data[j].goods
			  for (var i in list) {
				  data[j].goods[i].checked = data[j].checked
			  }
      for (var j in data) {
			  if (data[j].checked) {
				  length += list.length
			  }
			  totalLength += list.length
		  }
		  if (length == totalLength) {
			  status = true
		  } else {
			  status = false
		  }
		  this.$parent.$emit('change-footer-status', status)
		  if (!this.isCheckedAll) {
			  this.renderCart()
		  }
		  this.cartList = Object.assign([], data)
	  },
    /*
		 *  reduceNumber: 数量减少
		 *  @param: id 当前减少的购物车id
		 *  @param: amount 数量
		 *  @param： i 当前减少的购物车组的index
		 *  @param： index 当前减少的购物车的index
		 */
    reduceNumber (id, amount, i, index) {
      if (amount > 1) {
        amount--
        this.updateCartQuantity(id, amount, i, index)
      } else {
        Toast({
          message: '受不了了， 宝贝不能再少了'
        })
      }
    },

    /*
		 *  addNumber: 数量增加
		 *  @param: id 当前增加的购物车id
		 *  @param: amount 数量
		 *  @param： i 当前增加的购物车组的index
		 *  @param： index 当前增加的购物车的index
		 */
    addNumber (id, amount, i, index) {
      amount++
      this.updateCartQuantity(id, amount, i, index)
    },

    /*
		 * updateCartQuantity: 商品数量加减更新数
		 * @param: id 当前减少的购物车id
		 * @param: amount 数量
		 * @param： i 当前购物车组的index
		 * @param： index 当前购物车的index
		 */
    updateCartQuantity (id, amount, i, index) {
	  Indicator.open(this.indicator)
      cartUpdate(id, amount).then(
        res => {
          if (res) {
            Indicator.close(this.indicator)
            this.cartList[i].goods[index].goods_num = amount
            this.renderCart()
            // this.getCartNumber()
          }
        },
        error => {
          Toast(error.message)
          Indicator.close(this.indicator)
        }
      )
    },
    /*
		 * getCartNumber： 获取购物车列表
		 */
    getCartNumber () {
      cartQuantity().then(res => {
        if (res) {
          this.setCartNumber(res.quantity)
        }
      })
    },

    /*
		 *  goDetail: 跳转到详情
		 */
    goDetail (id) {
      this.$router.push({ name: 'HomeGoodsdetail', query: { goods_id: id } })
    },
	  goVoucher (index) {
		  this.saveVoucher(this.cartList[index].voucher_list)
		  this.$router.push({ name: 'HomeStoreVoucher' })
	  }

  }
}
</script>

<style lang="scss" scoped>
.cart-list-wrapper {
	overflow-y: auto;
	position: fixed;
	width: 100%;
	bottom: 2.5rem;
	top: 2.2rem;
	margin-top: 0.4rem;
	margin-bottom: 0.5rem;
	.store-info{background:#fff;border-bottom:1px solid #e8eaed;display:flex;padding-left:0.6rem;align-content: center;align-items: center;
	.store-name{font-size:.8rem;line-height: 2rem;flex:1;}
		.voucher{width:2rem;font-size:.7rem;color:$primaryColor;border:1px solid $primaryColor;margin-right: .5rem;text-align: center;border-radius:.1rem;}
	}
	.list-checkbox {
		width: 1rem;
		height: 1rem;
		flex-basis: 1rem;
		flex-shrink: 0;
		position: relative;
		margin-right:0.25rem;
		label {
			position: absolute;
			left:0;
			top: 0;
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
			width: 1rem;
			margin: 0;
			opacity: 0;
			background-color: #fff;
			&:checked + label {
				border-color:$primaryColor;
				background-color:$primaryColor;
				.iconfont{display: block;color:#fff}
			}
			&:focus {
				outline-offset: 0;
			}
		}
	}
	.list {
		background-color: #fff;
		padding: 0.6rem;
		border-bottom: 1px solid #e8eaed;
		display: flex;
		align-content: center;
		align-items: center;

		.list-item {
			display: flex;
			width: 100%;
			flex-direction: column;
			div.item {
				display: flex;
				width: 100%;
				div.ui-image {
					flex-shrink: 0;
					width: 4.5rem;
					height: 4.5rem;
					flex-basis: 4.5rem;
					position: relative;
					img {
						width: 100%;
						height: 100%;
						border: 1px solid #e8eaed;
						border-radius: 0.15rem;
					}
					span.promos {
						position: absolute;

						width: 1.8rem;
						height: 0.95rem;
						color: #ffffff;
						font-size: 0.5rem;
						top: 0;
						/* left: 0; */
						background-size: cover;
						font-weight: 100;
						line-height: 0.95rem;
						text-align: left;
						padding-left:0.25rem;
					}
					span.stock-info {
						position: absolute;
						height: 1rem;
						background: rgba(243, 244, 245, 1);
						line-height: 1rem;
						text-align: center;
						font-size: 0.7rem;
						color: $primaryColor;
						width: 100%;
						bottom: 0;
						left: 0;
					}
				}
				div.list-info {
					margin-left: 0.5rem;
					width: 100%;
					display: flex;
					flex-direction: column;
					align-content: center;
					justify-content: space-between;
					.product-header {
						display: flex;
						align-items: center;
						.promos-icon {
							width: 0.8rem;
							height: 0.8rem;
							margin-right: 0.2rem;
						}
						.product-title {
							font-size: 0.6rem;
							color: rgba(78, 84, 93, 1);
							padding: 0;
							display: -webkit-box;
							-webkit-box-orient: vertical;
							-webkit-line-clamp: 2;
							overflow: hidden;
							&.disabled-list {
								color: #a4aab3;
							}
						}
					}
					h3 {
						font-size: 0.7rem;
						color: rgba(78, 84, 93, 1);
						padding: 0;
						margin: 0;
						display: -webkit-box;
						-webkit-box-orient: vertical;
						-webkit-line-clamp: 2;
						overflow: hidden;
						&.disabled-list {
							color: #a4aab3;
						}
					}
					h3.property-info {
						font-size: 0.6rem;
						color: #7c7f88;
					}
					div.info-price {
						width: 100%;
						display: flex;
						justify-content: space-between;
						align-content: flex-end;
						align-items: flex-end;
						p {
							font-size: 0.85rem;
							color: $primaryColor;
							padding: 0;
							margin: 0;
							display: inline-block;
							&.disabled-list {
								color: #a4aab3;
							}
						}
					}
					div.ui-number {
						height: 1.2rem;
						display: flex;
						border-radius: 0.15rem 0 0 0.15rem;
						input,
						div {
							height: 1.2rem;
							text-align: center;
							color: #404245;
							display: inline-block;
							padding: 0;
							margin: 0;
							border: 0;
							outline-offset: 0;
						}
						.ui-common {
							line-height: 1.2rem;
							width: 1.3rem;
							height: 1.2rem;
							border: 1px solid #c7c7cd;
							cursor: pointer;
						}
						.reduce {
							border-right: 0;
						}
						.reduce-opacity {
							opacity: 0.4;
						}
						.add {
							border-left: 0;
						}
						input[type='number'] {
							width: 1.3rem;
							border: 1px solid #c7c7cd;
							border-radius: 0;
							border-image-width: 0;
							box-shadow: 0;
							vertical-align: bottom;
							&:focus {
								outline: none;
							}
						}
					}
				}
			}
			p.list-promotion-info {
				margin-top: 0.6rem;
				padding: 0.4rem 0;
				line-height: auto;
				font-size: 0.5rem;
				color: #000;
				background: #f8f8f8;
				width: 100%;
				span {
					border: 1px solid $primaryColor;
					padding: 1px 0.2rem;
					border-radius: 0.1rem;
					font-size: 0.5rem;
					color: $primaryColor;
					margin: 0 0.5rem;
					text-align: center;
				}
			}
		}
	}
}
.has-bottom {
	bottom: 4.7rem;
}
</style>
