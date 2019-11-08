<!-- recommend.vue -->
<template>
	<div class="ui-recommend-wrapper" v-if="commendList.length > 0">
		<div class="wrapper-swipe">
			<h2>平台推荐</h2>
					<div class="image-swipe-wrapper">
						<div
								v-for="(item,index) in commendList" :key="index"
							@click="goDetail(item.pgoods_id)"
							v-bind:style="getItemStyle"
						>
							<img
								:src="item.pgoods_image"
							/>

							<span>{{ item.pgoods_points }}积分</span>
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
      commendList: state => state.pointsgoods.commendList,
      currentProductId: state => state.pointsgoods.currentProductId
    }),

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

    goDetail (id) {
      let data = Object.assign({}, { id: id })
      this.$router.push({ name: 'HomePointsgoodsDetail', query: { pgoods_id: id } })
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
		padding:0.7rem;
		h2{font-size:.7rem;margin-bottom:0.5rem;}
	}

	.swiper-indicators {
		position: relative;
		margin-top:1rem;
		bottom:0;
	}

	.ui-recommend-all {
		height:2rem;
		background: #ffffff;
		border-top: 1px solid #e8eaed;
		font-size:0.8rem;
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
				padding:0;
				margin:0;
			}
			span {
				position: absolute;
				left:0;
				bottom: 0;
				width: 100%;
				display: inline-block;
				height:1.2rem;
				line-height:1.2rem;
				font-size:0.8rem;
				color: #ffffff;
				background: rgba(0, 0, 0, 0.5);
				border-radius: 0 0 0.1rem 0.1rem;
			}
		}
	}
}
</style>
