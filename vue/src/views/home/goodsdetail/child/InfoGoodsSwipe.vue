<!-- GoodsSwipe.vue -->
<template>
	<div class="swipe-wrapper ui-common-swiper" v-if="detailInfo">
		<!-- 轮播图 -->
		<mt-swipe
			:auto="0"
			class="ui-common-swiper"
			:prevent="false"
			:stop-propagation="true"
			@change="handleChange"
		>
			<mt-swipe-item
				v-for="(item, index) in detailInfo.photos"
				v-bind:key="index"
				v-if="detailInfo.photos && detailInfo.photos.length > 0"
			>
				<img v-bind:src="item" v-on:click="setPopupVisible(index)" />
			</mt-swipe-item>
			<mt-swipe-item v-if="!detailInfo.photos || detailInfo.photos.length <= 0">
				<img
					src="../../../../assets/image/home/default_image_banner.png"
					class="product-img"
				/>
			</mt-swipe-item>
		</mt-swipe>

	</div>
</template>

<script>
import PreviewPicture from './PreviewPicture'
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {
      popupVisible: false,
      index: 0
    }
  },

  components: {
    'v-picture': PreviewPicture
  },

  created () {
    this.$on('hide-priview-picture', value => {
      this.popupVisible = value
      this.setisPreviewPicture(value)
    })
  },

  computed: {
    ...mapState({
      detailInfo: state => state.goodsdetail.detailInfo
    })
  },

  methods: {
    ...mapMutations({
      setisPreviewPicture: 'setisPreviewPicture',
      setSwiperId: 'setSwiperId'
    }),

    setPopupVisible () {
      this.popupVisible = true
      this.setisPreviewPicture(true)
    },

    handleChange (index) {
      this.index = index
      this.setSwiperId(index)
    }
  }
}
</script>

<style lang="scss">
.ui-common-swiper {
	width: 100%;
	height: 15rem !important;
	.mint-swipe-items-wrap {
		.mint-swipe-item {
			text-align: center;
			overflow: hidden;
			img {
				height: 100%;
				width: auto;
			}
		}
	}
	.mint-swipe-indicators {
		div.mint-swipe-indicator {
			background: #efeff4;
			opacity: 1;
			&.is-active {
				background: $primaryColor;
			}
		}
	}
}
</style>
