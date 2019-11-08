<!-- Shopping.vue -->
<template>
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
						v-bind:src="detailInfo.pgoods_image"
						class="info-image"
						v-if="detailInfo.pgoods_image"
					/>
          <img
          	src="../../../../assets/image/home/default_image_banner.png"
          	class="info-image"
          	v-else
          />
					<div>

						<span
							class="header-price"
							>{{ detailInfo.pgoods_points }}积分</span
						>

						<span>

						</span>
						<span>库存{{ currentStock }}件</span>
						<span>数量：{{ numbers }}</span>
					</div>

					<span class="iconfont close" v-on:click="closeCartInfo(false)">&#xe65a;</span>
				</div>

				<div class="goods-detail-properties">

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
					>
						加入购物车
					</div>
					<div
						class="footer-button active-buy"
						v-on:click="checkProduct(true)"
					>
						立即购买
					</div>

				</div>
			</div>
		</div>
	</mt-popup>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import { Toast } from 'mint-ui'
import { cartAdd } from '../../../../api/memberPointscart'

export default {
  data () {
    return {
      numbers: this.$store.state.pointsgoods.number > 0 ? this.$store.state.pointsgoods.number : 1, // todo 临时解决
      currentStock: this.$store.state.pointsgoods.detailInfo.pgoods_storage,
      productId: this.$store.state.pointsgoods.currentProductId,
      toastConfig: {
        message: '商品达到每单限购数量',
        position: 'middle'
      },
      ids: [],
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
  },

  computed: {
    ...mapState({
      isOnline: state => state.member.isOnline,
      detailInfo: state => state.pointsgoods.detailInfo,
      number: state => state.pointsgoods.number
    })
  },

  watch: {
    numbers: function (value) {
      if (value) {
        let pgoodsStorage = this.detailInfo.pgoods_storage
        if (value <= 0) {
          this.numbers = 1
          this.toastConfig.message = '受不了了，宝贝不能再少了'
          Toast(this.toastConfig)
        } else if (value > pgoodsStorage) {
          this.toastConfig.message = '商品库存不足'
          Toast(this.toastConfig)
          this.numbers = pgoodsStorage
        }
        this.saveNumber(this.numbers)
      }
    }
  },

  mounted () {},

  methods: {
    ...mapMutations({
      saveCartState: 'saveCartState',
      saveNumber: 'saveNumber',
    }),

    // 关闭购物车浮层
    closeCartInfo (value) {
      this.saveCartState(value)
    },

    // 数量加
    addNumber () {
      if (this.detailInfo.pgoods_storage && this.numbers > this.detailInfo.pgoods_storage) {
        this.toastConfig.message = '商品库存不足'
        Toast(this.toastConfig)
        this.numbers = this.detailInfo.pgoods_storage
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
          this.$router.push({ name: 'MemberPointsBuyStep1', query: { buy_now: 1, cart_id: this.detailInfo.pgoods_id + '|' + this.numbers } })
        } else {
          cartAdd(this.detailInfo.pgoods_id, this.numbers).then(
            res => {
              this.$parent.$emit('update-cart-num')
              Toast(res.message)
            },
            error => {
              Toast(error.message)
            }
          )
        }
      }
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
			padding: 0.75rem;
			display: flex;
			justify-content: flex-start;
			padding-bottom: 1.25rem;
			border-bottom: 0.5px solid rgba(232, 234, 237, 1);
			img.info-image {
				width: 6rem;
				height: 6rem;
				border-radius: 1px;
				margin-top: -0.75rem;
				position: absolute;
				top: -0.65rem;
			}
			div {
				padding-left: 6.75rem;
				width: 100%;
				span {
					display: block;
					color: #8f8e94;
					&.header-price {
						font-size: 0.9rem;
						line-height: 1rem;
						padding-bottom: 0.6rem;
						color: $primaryColor;
					}
					&:nth-child(2) {
						img {
							vertical-align: middle;
							padding-right: 0.5rem;
							width: 2.5rem;
							height: 1rem;
						}
						span {
							display: inline;
							font-size: 0.7rem;
							line-height: 0.7rem;
							padding-bottom: 0.45rem;
							padding-top: 0.6rem;
						}
					}
					&:nth-child(3) {
						line-height: 1rem;
						font-size: 0.7rem;
						line-height: 1rem;
						width: 100%;
						overflow: hidden;
						text-overflow: ellipsis;
						white-space: nowrap;
						padding-right: 0.75rem;
					}
					&:last-child {
						line-height: 1rem;
						font-size: 0.7rem;
						line-height: 1rem;
						padding-top: 0.6rem;
						width: 100%;
						overflow: hidden;
						text-overflow: ellipsis;
						white-space: nowrap;
						padding-right: 0.75rem;
					}
				}
			}
			img.close {
				position: absolute;
				top: 0.75rem;
				right: 0.75rem;
				width: 0.65rem;
				height: 0.65rem;
				cursor: pointer;
				opacity: 1;
			}
		}
		div.goods-detail-properties {
			width: 100%;
			overflow: auto;
			height: auto;
			position: absolute;
			top: 11.2rem;
			bottom: 2.2rem;
			/* height: 100%; */
		}
		div.goods-properties {
			padding: 0.75rem 0;
			p {
				font-size: 0.8rem;
				color: rgba(41, 43, 45, 1);
				line-height: 0.8rem;
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
						border-radius: 0.1rem;
						cursor: pointer;
						margin-top: 0.75rem;
						&.active-properties {
							background: $primaryColor;
							color: rgba(255, 255, 255, 1);
							border: 1px solid $primaryColor;
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
			padding: 0.75rem 0.75rem 1rem 0.75rem;
			p {
				font-size: 0.8rem;
				color: rgba(41, 43, 45, 1);
				line-height: 0.8rem;
				padding: 0;
				margin: 0;
				padding-bottom: 0.8rem;
			}
			div.ui-number {
				height: 1.5rem;
				display: flex;
				border-radius: 0.15rem 0 0 0.15rem;
				input,
				div {
					height: 1.4rem;
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
					line-height: 1.4rem;
					width: 1.4rem;
					height: 1.4rem;
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
					width: 2rem;
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
				line-height: 2.2rem;
				text-align: center;
				font-size: 0.8rem;
				height: 2.2rem;
			}
			.active-cart {
				background: $primaryColor;
			}
			.active-buy {
				background: #000;
			}
		}
	}
}
</style>
