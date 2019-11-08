<!-- GoodsSwipe.vue -->
<template>
	<div class="swipe-wrapper ui-common-swiper" v-if="detailInfo">
		<img v-if="detailInfo.pgoods_image" v-bind:src="detailInfo.pgoods_image" @click="setPopupVisible()" />
    <img
      v-else
    	src="../../../../assets/image/home/default_image_banner.png"
    	class="product-img"
    />
	</div>
</template>

<script>
import PreviewPicture from './PreviewPicture'
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {
      popupVisible: false,
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
      detailInfo: state => state.pointsgoods.detailInfo
    })
  },

  methods: {
    ...mapMutations({
      setisPreviewPicture: 'setisPreviewPicture',
    }),

    setPopupVisible () {
      this.popupVisible = true
      this.setisPreviewPicture(true)
    },

  }
}
</script>

<style lang="scss">
.ui-common-swiper {
	width: 100%;
	height: 15rem !important;
  text-align:center;
	img {
				height: 100%;
				width: auto;
        max-width: 100%;
			}

}
</style>
