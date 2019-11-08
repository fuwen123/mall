<!-- Shopping.vue -->
<template>
<div class="container">
	<mt-popup
		v-model="isShowcartInfo"
		position="bottom"
		v-if="detailInfo"
		v-bind:close-on-click-modal="false"
	>
		<div class="ui-add-shopping" v-if="detailInfo">
			<div class="shopping-info">
				<div class="info-header">
					<img
						src="../../../../assets/image/home/default_image_banner.png"
						class="info-image"
						v-if="!detailInfo.photos || detailInfo.photos.length <= 0"
					/>
					<img
						v-bind:src="detailInfo.photos[0]"
						class="info-image"
						v-if="detailInfo.photos && detailInfo.photos.length > 0"
					/>
					<div>
						<span class="header-price" >￥{{ propertiPrice }}</span>
						<span>
							<span class="iconfont" v-if="detailInfo.activity">&#xe631;</span>
							<span v-if="detailInfo.activity">{{
								detailInfo.activity.name
							}}</span>
						</span>
						<!-- {{ chooseinfo}} -->
						<span>库存{{ currentStock }}件</span>
						<span>数量：{{ numbers }}</span>
					</div>

					<span class="iconfont close" v-on:click="closeCartInfo(false)">&#xe65a;</span>
				</div>

				<div class="goods-detail-properties">
					<div
						class="goods-properties"
						v-if="
							detailInfo &&
								detailInfo.goods_spec
						"
						v-for="(item, index) in detailInfo.spec_name"
						:key="index"
					>
						<p>{{ item }}</p>
						<div class="properties-list">
							<div v-for="(key, keyindex) in detailInfo.spec_value[index]" :key="keyindex">
								<span
									@click="setCurrentIndex(keyindex,detailInfo.spec_value[index])"
									v-bind:class="{
										'active-properties': detailInfo.goods_spec[keyindex]
									}"
									>{{ key }}</span
								>

							</div>
						</div>
					</div>
					<div class="info-body" id="info-body">
						<p>数量</p>
						<div class="ui-number">
							<div
								class="reduce ui-common"
								@click.stop="reduceNumber()"
								v-bind:class="{ 'reduce-opacity': numbers <= 1 }"
							>
								-
							</div>
							<input
								type="number"
								min="1"
								class="number"
								value="1"
								v-model="numbers"
								readonly="true"
							/>
							<div class="add ui-common" @click.stop="addNumber()">+</div>
						</div>
					</div>
				</div>
				<div class="info-footer">
					<div
						class="footer-button active-cart"
						v-on:click="checkProduct(false)"
						v-if="!detailInfo.bargain_info && ((!detailInfo.pintuan_type && detailInfo.cart) || (detailInfo.pintuan_type && detailInfo.pintuangroup_list.length))"
					>
						{{addCartText}}
					</div>
					<div
						class="footer-button active-buy"
						v-on:click="checkProduct(true)"
					>
						{{buyNowText}}
					</div>

				</div>
			</div>
		</div>
	</mt-popup>
	<mt-popup v-if="detailInfo.pintuan_type && detailInfo.pintuangroup_list.length" v-model="subjectVisible" position="right" class="common-popup-wrapper">
		<div class="common-header-wrap">
			<mt-header title="拼团列表" class="common-header">
				<mt-button slot="left" icon="back" @click="subjectVisible=false"></mt-button>
			</mt-header>
		</div>
		<div class="common-popup-content">

			<div v-for="(item, index) in detailInfo.pintuangroup_list" @click="goPintuan(item)">
				<mt-cell :title="item.member_name" is-link v-if="!(detailInfo.pintuangroup_share_id>0 && item.pintuangroup_headid != detailInfo.pintuangroup_share_id)"><!--通过拼团分享会员ID判断其他组团是否显示-->
					<span>{{item.pintuangroup_surplus>0?('还差'+item.pintuangroup_surplus+'人'):'已满员'}}</span>
					<img slot="icon" :src="item.pintuangroup_avatar" width="24" height="24">
				</mt-cell>

			</div>
		</div>
	</mt-popup>
</div>
</template>

<script>
import { mapState, mapMutations, mapActions } from 'vuex'
import { Toast } from 'mint-ui'
import { cartAdd } from '../../../../api/homecart'
import { addBargain } from '../../../../api/memberBargain'
export default {
  data () {
    return {
      pintuangroup_id: 0,
      subjectVisible: false,
      addCartText: '加入购物车',
      buyNowText: '立即购买',
      numbers: this.$store.state.goodsdetail.number > 0 ? this.$store.state.goodsdetail.number : 1, // todo 临时解决
      currentStock: this.$store.state.goodsdetail.detailInfo.goods_storage,
      productId: this.$store.state.goodsdetail.currentProductId,
      toastConfig: {
        message: '商品达到每单限购数量',
        position: 'middle'
      },
      ids: [],
      info: [],
      propertiPrice: 0,
      earlier: {}
    }
  },

  props: {
    isShowcartInfo: {
      type: Boolean,
      default: false
    }
  },

  created () {
  	if (this.detailInfo.bargain_info) {
  		if (this.detailInfo.bargainorder_info) {
        if (this.detailInfo.bargainorder_info.bargainorder_state == 2) {
          this.buyNowText = '砍价成功去下单'
        } else {
          this.buyNowText = '正在砍价'
        }
      } else {
        this.buyNowText = '发起砍价'
      }
    } else if (this.detailInfo.pintuan_type) {
      this.addCartText = '参加拼团'
      this.buyNowText = '立刻开团'
      this.propertiPrice = this.detailInfo.pintuan_price
    } else if (this.detailInfo.is_presell == 1) {
      this.buyNowText = '预售购买（' + this.$moment.unix(this.detailInfo.presell_deliverdate).format('YYYY年MM月DD日') + '发货）'
    } else if (this.detailInfo.is_goodsfcode == 1) {
      this.buyNowText = 'F码购买'
    } else {
      if (this.detailInfo.promotion_type) {
        this.propertiPrice = this.detailInfo.promotion_price
      } else {
        this.propertiPrice = this.detailInfo.goods_price
      }
    }
    this.info = this.chooseinfo.specification
    this.ids = Object.assign([], this.chooseinfo.ids)
  },

  computed: {
    ...mapState({
      isOnline: state => state.member.isOnline,
      token: state => state.member.token,
      detailInfo: state => state.goodsdetail.detailInfo,
      specList: state => state.goodsdetail.specList,
      number: state => state.goodsdetail.number,
      chooseinfo: state => state.goodsdetail.chooseinfo
    })
  },

  watch: {
    numbers: function (value) {
      if (value) {
        let goodStorage = this.detailInfo.goods_storage
        if (value <= 0) {
          this.numbers = 1
          this.toastConfig.message = '受不了了，宝贝不能再少了'
          Toast(this.toastConfig)
        } else if (value > goodStorage) {
          this.toastConfig.message = '商品库存不足'
          Toast(this.toastConfig)
          this.numbers = goodStorage
        }
        this.saveNumber(this.numbers)
      }
    }
  },

  mounted () {},

  methods: {
	  ...mapActions({
		  getGoodsDetail: 'getGoodsDetail'
	  }),
    ...mapMutations({
      saveCartState: 'saveCartState',
      saveNumber: 'saveNumber',
      saveChooseInfo: 'saveChooseInfo',
      saveProperties: 'saveProperties',
      setCartNumber: 'setCartNumber',
      saveSelectedCartGoods: 'saveSelectedCartGoods'
    }),
    goPintuan (item) {
      if (item.pintuangroup_surplus > 0) {
        this.pintuangroup_id = item.pintuangroup_id
        this.checkProduct(true)
      }
    },
    // 关闭购物车浮层
    closeCartInfo (value) {
      this.saveCartState(value)
    },

    // 数量加
    addNumber () {
      if (this.detailInfo.goods_storage && this.numbers > this.detailInfo.goods_storage) {
        this.toastConfig.message = '商品库存不足'
        Toast(this.toastConfig)
        this.numbers = this.detailInfo.goods_storage
      } else {
        this.numbers++
      }
    },

    // 数量减
    reduceNumber () {
      if (this.numbers > 1) {
        this.numbers--
      } else {
        this.numbers = 1
        this.toastConfig.message = '受不了了，宝贝不能再少了'
        Toast(this.toastConfig)
      }
    },

    // 加入购物车
    checkProduct (checkout) {
      if (!this.isOnline) {
        this.$router.push({ name: 'HomeMemberLogin' })
      } else {
        if (checkout) { // 立即购买
          if (this.detailInfo.is_virtual == '1') {
            let params = { goods_id: this.detailInfo.goods_id, quantity: this.numbers }
            if (this.detailInfo.pintuan_type) {
              params['pintuan_id'] = this.detailInfo.pintuan_id
              params['pintuangroup_id'] = this.pintuangroup_id
            }
            this.$router.push({ name: 'MemberVrBuyStep1', query: params })
          } else {
            let params = { buy_now: 1, cart_id: this.detailInfo.goods_id + '|' + this.numbers }
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
              params['pintuangroup_id'] = this.pintuangroup_id
            }
            this.$router.push({ name: 'MemberBuyStep1', query: params })
          }
        } else {
          if (this.detailInfo.pintuan_type) {
            this.subjectVisible = true
          } else {
            cartAdd(this.detailInfo.goods_id, this.numbers).then(
              res => {
                this.$parent.$emit('update-cart-num')
                this.$parent.$emit('end-addcart-animation')
                Toast(res.message)
              },
              error => {
                Toast(error.message)
              }
            )
          }
        }
      }
    },

    keyDown (event) {},

    /*
		 * setCurrentIndex: 设置当前选中的规格的id,
		 * @parmas: index 当前规格的index
		 * @parmas: specList 同类规格的列表
		 */
    setCurrentIndex (index, specList) {
      let specIndex = []
      for (var k in specList) {
        if (typeof (this.detailInfo.goods_spec[k]) !== 'undefined') {
          delete this.detailInfo.goods_spec[k]
          this.detailInfo.goods_spec[index] = specList[index]
          break
        }
      }
      for (var k in this.detailInfo.goods_spec) {
        specIndex.push(k)
      }
      specIndex = specIndex.sort(this.sortNumber).join('|')
      this.getGoodsDetail({ goods_id: this.specList[specIndex], token: this.token, extra: {} }).catch(error => {
		  Toast(error.message)
	  })
    },
    sortNumber (a, b) {
      return a - b
    }
  }
}
</script>

<style lang="scss" scoped>
.mint-popup-bottom {
	overflow: initial;
	height: 70%;
}
.ui-add-shopping {
	/* position: fixed;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	width: 100%;
	height: 100%; */
	/* z-index: 200; */
	/* background:rgba(0,0,0, 0.4); */
	.shopping-info {
		background: rgba(255, 255, 255, 1);
		height: 100%;
		position: absolute;
		width: -webkit-fill-available;
		/* bottom: 0; */
		z-index: 10;
		width: 100%;
		.info-header {
			padding:0.75rem;
			display: flex;
			justify-content: flex-start;
			padding-bottom:1.25rem;
			border-bottom: 0.5px solid rgba(232, 234, 237, 1);
			img.info-image {
				width:6rem;
				height:6rem;
				border-radius: 1px;
				margin-top: -0.75rem;
				position: absolute;
				top: -0.65rem;
			}
			div {
				padding-left:67.5rem;
				width: 100%;
				span {
					display: block;
					color: #8f8e94;
					&.header-price {
						font-size:0.9rem;
						line-height:1rem;
						padding-bottom:0.6rem;
						color: $mainColor;
					}
					&:nth-child(2) {
						img {
							vertical-align: middle;
							padding-right:0.5rem;
							width:2.5rem;
							height:1rem;
						}
						span {
							display: inline;
							font-size:0.7rem;
							line-height:0.7rem;
							padding-bottom:0.45rem;
							padding-top:0.6rem;
						}
					}
					&:nth-child(3) {
						line-height:1rem;
						font-size:0.7rem;
						line-height:1rem;
						width: 100%;
						overflow: hidden;
						text-overflow: ellipsis;
						white-space: nowrap;
						padding-right:0.75rem;
					}
					&:last-child {
						line-height:1rem;
						font-size:0.7rem;
						line-height:1rem;
						padding-top:0.6rem;
						width: 100%;
						overflow: hidden;
						text-overflow: ellipsis;
						white-space: nowrap;
						padding-right:0.75rem;
					}
				}
			}
			.close {
				position: absolute;
				top:0.75rem;
				right:0.75rem;
				width:0.65rem;
				height:0.65rem;
				cursor: pointer;
				opacity: 1;
			}
		}
		div.goods-detail-properties {
			width: 100%;
			overflow: auto;
			height: auto;
			position: absolute;
			top:6.2rem;
			bottom:2.2rem;
			/* height: 100%; */
		}
		div.goods-properties {
			padding: 0.75rem 0;
			p {
				font-size:0.7rem;
				color: rgba(41, 43, 45, 1);
				line-height:0.8rem;
				margin: 0;
				padding: 0 0.75rem;
			}
			div.properties-list {
				div {
					display: inline-block;
					margin-left: 0.75rem;
					span {
						font-size: 0.7rem;
						color: rgba(78, 84, 93, 1);
						line-height: 0.7rem;
						display: inline-block;
						padding: 0.35rem 0.7rem;
						border: 1px solid #404245;
						border-radius:0.1rem;
						cursor: pointer;
						margin-top:0.75rem;
						&.active-properties {
							background: $mainColor;
							color: rgba(255, 255, 255, 1);
							border: 1px solid $mainColor;
						}
						&.disabled-properties {
							color: #b1b5bb;
							cursor: none;
							border: 1px solid #a2a6ad;
						}
					}
					/* &:nth-child(even) {
					margin-left: 0.75rem;
					} */
				}
			}
		}
		.info-body {
			padding:0.75rem 0.75rem 1rem 0.75rem;
			p {
				font-size:0.7rem;
				color: rgba(41, 43, 45, 1);
				line-height:0.8rem;
				padding: 0;
				margin: 0;
				padding-bottom:0.8rem;
			}
			div.ui-number {
				height: 1.5rem;
				display: flex;
				border-radius: 0.15rem 0 0 0.15rem;
				input,
				div {
					height:1.4rem;
					line-height:1.4rem;
					text-align: center;
					color: #404245;
					display: inline-block;
					padding: 0;
					margin: 0;
					border: 0;
					outline-offset: 0;
				}
				.ui-common {
					line-height:1.4rem;
					width:1.4rem ;
					height:1.4rem;
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
					width:2rem;
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
		.info-footer {
			width: 100%;
			position: fixed;
			bottom: 0;
			display: flex;
			.footer-button {
				flex: 1;
				color: #ffffff;
				line-height:2.2rem;
				text-align: center;
				font-size:0.7rem;
				height:2.2rem;
			}
			.active-cart {
				background: $mainColor;
			}
			.active-buy {
				background: #000;
			}
		}
	}

}

</style>
