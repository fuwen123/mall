<!-- recommend.vue -->
<template>
	<div class="ui-recommend-wrapper" v-if="commendList.length > 0">
		<div class="wrapper-swipe">
			<h2>店铺推荐</h2>
					<div class="image-swipe-wrapper">
						<div
								v-for="(item,index) in commendList" :key="index"
							@click="goDetail(item.goods_id)"
							v-bind:style="getItemStyle"
						>
							<img
								:src="item.goods_image_url"
							/>

							<span>￥{{ item.goods_price }}</span>
						</div>
					</div>

		</div>

	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {
      list: [],
      indicatorArray: [],
      currentIndex: 0
    }
  },

  created () {
  },

  computed: {
    ...mapState({
      commendList: state => state.goodsdetail.commendList,
      currentProductId: state => state.goodsdetail.currentProductId
    }),
    getWrapperStyle: function () {
      const { width, height } = window.screen
      let itemWidth = width - 30
      let itemHeight = (width - 30 - 16) / 3
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
  },

  methods: {

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
    goRecommend () {
      let params = {}
      if (this.$route.params.brand) {
        params.brand = this.$route.params.brand
      }
      if (this.$route.params.category) {
        params.category = this.$route.params.category
      }
      if (this.$route.params.shop) {
        params.shop = this.$route.params.shop
      }
      params.product = this.currentProductId
      this.$router.push({ name: 'recommend', params: params })
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
	margin-top:.4rem;
	margin-bottom:0.5rem;
	.wrapper-swipe {
		padding:0.7rem;
		h2{font-size:.7rem;margin-bottom:0.5rem;}
	}

	.swiper-indicators {
		position: relative;
		margin-top:1rem;
		bottom: 0;
	}

	.ui-recommend-all {
		height:2rem;
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
		justify-content: space-between;
		align-content: center;
		align-items: center;
		overflow: hidden;
		div {
			float:left;
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
