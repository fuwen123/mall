<!-- PreviewPicture.vue -->
<template>
	<div v-if="detailInfo">
		<mt-popup position="right" class="common-popup-wrapper" v-model="isshow"  popup-transition="popup-fade">
			<div class="preview-picture">
				<div
					class="picture-header"
					v-on:click="closePopup()"
				>
					<span>关闭</span
					><span>商品详情</span
					><span v-if="detailInfo.photos"
						>{{ defaultindex + 1 }} / {{ detailInfo.photos.length }}</span
					>
				</div>

				<div class="picture-body">
					<img v-if="detailInfo.pgoods_image" v-bind:src="detailInfo.pgoods_image" @click="setPopHeader()" />
							<img
                v-else
								src="../../../../assets/image/home/default_image_banner.png"
								class="product-img"
								@click="setPopHeader()"
							/>
				</div>
			</div>
		</mt-popup>
	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {
      isshowPopHeader: false
    }
  },

  props: {
    isshow: {
      type: Boolean,
      default: false
    },
   
  },

  computed: {
    ...mapState({
      detailInfo: state => state.pointsgoods.detailInfo
    })
  },

  methods: {
    ...mapMutations({
      setisPreviewPicture: 'setisPreviewPicture'
    }),


    /*
		 *  closePopup: 关闭图片预览
		 */
    closePopup () {
      this.setisPreviewPicture(false)
      this.$parent.$emit('hide-priview-picture', false)
    },

    /*
		 * setPopHeader: 预览大图点击图片切换header
		 */
    setPopHeader (ev) {
      this.isshowPopHeader = !this.isshowPopHeader
    }
  }
}
</script>

<style lang="scss" scoped>

.preview-picture {
	width: 100%;
	height: 100%;
	position: fixed;
	z-index: 10;
	top: 0;
	bottom: 0;
	left: 0;
	right: 0;
	background-color: #000;
	.picture-header {
		height:2.2rem;
		color: #000;
		background-color: #fff;
		display: flex;
		justify-content: center;
		align-content: center;
		align-items: center;
		width: 100%;
		top: 0;
		span {
			font-size:0.7rem;
			font-weight: normal;
			&:first-child {
				cursor: pointer;
				position: absolute;
				left:0.75rem;
				background-size:1.2rem;
				display: inline-block;
				height:2.2rem;
				line-height:2.2rem;
			}
		}
	}
	.picture-body {
		position: absolute;
		top:2.2rem;
		bottom: 0;
		width: 100%;
		display: flex;
		justify-content: center;
		align-content: center;
		align-items: center;
    img{width:100%}
	}
}
</style>
