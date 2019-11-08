<!-- with.vue -->
<template>
	<div class="ui-recommend-wrapper" v-if="bundling_array">
		<div class="wrapper-swipe">
			<mt-swipe
				v-bind:style="getWrapperStyle"
				:auto="0"
				@change="handleChange"
				:default-index="currentIndex"
				:stop-propagation="true"
				:speed="100"
				:showIndicators="false"
			>
				<mt-swipe-item v-for="(item, index) in bundling_array" :key="index">
					<div class="image-swipe-wrapper">
						<div
							v-for="(image, j) in b_goods_array[index]"
							@click="goDetail(image.id)"
							v-bind:style="getItemStyle"
						>
							<img
								:src="image.image"
							/>

							<span>￥{{ image.price }}</span>
						</div>
					</div>
					<div class="ui-recommend-all" v-on:click="goRecommend(index)">购买组合</div>
				</mt-swipe-item>
			</mt-swipe>
		</div>

	</div>
</template>

<script>
import { Toast } from 'mint-ui'
import { productAccessoryList } from '../../../../api/homegoodsdetail'
import { cartAdd } from '../../../../api/homecart'
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {
      bundling_array: null,
      b_goods_array: null,
      indicatorArray: [],
      currentIndex: 0
    }
  },

  created () {
    this.getRecommendList()
  },

  computed: {
    ...mapState({
      currentProductId: state => state.goodsdetail.currentProductId
    }),

    getWrapperStyle: function () {
      const { width, height } = window.screen
      let itemWidth = width - 30
      let itemHeight = (width - 30 - 16) / 3 + 64
      return {
        width: itemWidth + 'px',
        height: itemHeight + 'px'
      }
    },
    getItemStyle: function () {
      const { width, height } = window.screen
      let itemWidth = (width - 30 - 16) / 3
      let itemHeight = itemWidth
      return {
        width: itemWidth + 'px',
        height: itemHeight + 'px'
      }
    }
    // CODE REVIEW: 代码格式
  },

  methods: {
    /*
				getRecommendList: 获取推荐商品
			*/
    getRecommendList () {
      let params = {
        product: this.currentProductId ? this.currentProductId : '',
        page: 1,
        per_page: 10
      }
      productAccessoryList(params.product).then(
        res => {
          if (res) {
            this.bundling_array = res.result.bundling_array
			  this.b_goods_array = res.result.b_goods_array
          }
        }
      )
    },

    /*
				buildList：构建促销展示商品的数据
				@params： res 接口数据返回的促销商品
			*/
    buildList (res) {
      let index = Math.ceil(res.length / 3)
      let newArray = []
      if (index) {
        for (let i = 0; i <= index - 1; i++) {
          let subArray = []
          subArray.push(res.slice(i * 3, i * 3 + 3))
          newArray.push(subArray)
        }
      }
      return newArray
    },

    /*
				buildSwipeIndicators: 根据轮播图的长度计算位于底部的按钮的个数
			*/
    buildSwipeIndicators () {
      let photos = this.list
      for (let i = 0, len = photos.length - 1; i <= len; i++) {
        photos[i].index = i
        this.indicatorArray.push(photos[i])
      }
    },

    /*
				handleChange: 查看大图的时候滑动大图设置位于底部的按钮的选中状态同时隐藏查看大图的头部信息
				@params: index 当前滑动的图片的index
			 */
    handleChange (index) {
      this.currentIndex = index
    },

    /*
	goRecommend: 跳转到相关商品页面
	*/
    goRecommend (id) {
      cartAdd(0, 0, id).then(
        res => {
          this.$router.push({ name: 'HomeCart' })
        },
        error => {
          Toast(error.message)
        }
      )
    },

    goDetail (id) {
      let data = Object.assign({}, { id: id })
      this.$router.push({ name: 'HomeGoodsdetail', query: { goods_id: id } })
    }
  }
}
</script>

<style lang="scss" scoped>
.ui-recommend-wrapper {
	background: #ffffff;
	margin-top:0.4rem;
	margin-bottom:0.5rem;
	.wrapper-swipe {
		padding:0.75rem 0.75rem 0 0.75rem;
		.mint-swipe {
			height:5rem;
			.mint-swipe-items-wrap {
				.mint-swipe-item {
				}
			}
		}
	}

	.swiper-indicators {
		position: relative;
		margin-top:1rem;
		bottom: 0;
	}

	.ui-recommend-all {
		height:2.2rem;
		background: #ffffff;
		border-top: 1px solid #e8eaed;
		font-size:0.7rem;
		color: rgba(78, 84, 93, 1);
		width: 100%;
		flex-basis: 100%;
		display: flex;
		justify-content: center;
		align-content: center;
		align-items: center;
		margin-top:1rem;
	}

	div.image-swipe-wrapper {
		overflow:hidden;
		justify-content: space-between;
		align-content: center;
		align-items: center;
		white-space: nowrap;
		div {
			display:inline-block;
			position: relative;
			border: 1px solid #efeff4;
			border-radius:0.1rem;
			img {
				width: 100%;
				height: 100%;
				padding: 0;
				margin: 0;
			}
			span {
				position: absolute;
				left:0;
				bottom: 0;
				width: 100%;
				display: inline-block;
				height:1.2rem;
				line-height:1.2rem;
				font-size:0.7rem;
				color: #ffffff;
				background: rgba(0, 0, 0, 0.5);
				border-radius: 0 0 0.1rem 0.1rem;
			}
		}
	}
}
</style>
