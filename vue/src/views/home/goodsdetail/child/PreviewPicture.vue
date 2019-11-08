<!-- PreviewPicture.vue -->
<template>
	<div v-if="detailInfo">
		<mt-popup v-model="isshow" popup-transition="popup-fade">
			<div class="preview-picture">
				<div
					class="picture-header"
					v-on:click="closePopup()"
					v-if="!isshowPopHeader"
				>
					<span>关闭</span
					><span v-if="detailInfo.photos"
						>{{ defaultindex + 1 }} / {{ detailInfo.photos.length }}</span
					>
				</div>

				<div class="picture-body">
					<mt-swipe
						:auto="0"
						:show-indicators="true"
						:default-index="defaultindex"
						class="ui-common-swiper"
						:prevent="false"
						:stop-propagation="true"
						@change="handleChange"
					>
						<mt-swipe-item
							v-for="(item, index) in detailInfo.photos"
							v-bind:key="index"
						>
							<img v-bind:src="item" @click="setPopHeader()" />
						</mt-swipe-item>
						<mt-swipe-item
							v-if="!detailInfo.photos || detailInfo.photos.length <= 0"
						>
							<img
								src="../../../../assets/image/home/default_image_banner.png"
								class="product-img"
								@click="setPopHeader()"
							/>
						</mt-swipe-item>
					</mt-swipe>
				</div>
			</div>
		</mt-popup>
	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
export default {
	data() {
		return {
			isshowPopHeader: false
		}
	},

	props: {
		isshow: {
			type: Boolean,
			default: false
		},
		defaultindex: {
			type: Number,
			default: 0
		}
	},

	computed: {
		...mapState({
			detailInfo: state => state.goodsdetail.detailInfo
		})
	},

	methods: {
		...mapMutations({
			setisPreviewPicture: 'setisPreviewPicture'
		}),

		/*
				handleChange: 轮播图改变时设置是否阻止事件冒泡
				@params: index 当前滑动的index
			 */
		handleChange(index) {
			this.defaultindex = index
		},

		/*
		 *  closePopup: 关闭图片预览
		 */
		closePopup() {
			this.setisPreviewPicture(false)
			this.$parent.$emit('hide-priview-picture', false)
		},

		/*
		 * setPopHeader: 预览大图点击图片切换header
		 */
		setPopHeader(ev) {
			this.isshowPopHeader = !this.isshowPopHeader
		}
	}
}
</script>

<style lang="scss" scoped>
.swipe-wrapper {
	width: 100%;
}
.mint-popup {
	width: 100%;
	height: 100%;
	background-color: #000;
}
.mint-swipe,
.mint-swipe-items-wrap {
	position: static;
}
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
		height: 2.2rem;
		color: #000;
		background-color: #fff;
		display: flex;
		justify-content: center;
		align-content: center;
		align-items: center;
		width: 100%;
		top: 0;
		span {
			font-size: 0.7rem;
			font-weight: normal;
			&:first-child {
				cursor: pointer;
				position: absolute;
				left: 0.75rem;

				background-size: 1.2rem;
				display: inline-block;
				height: 2.2rem;
				line-height: 2.2rem;
			}
		}
	}
	.picture-body {
		position: absolute;
		top: 2.2rem;
		bottom: 0;
		width: 100%;
		display: flex;
		justify-content: center;
		align-content: center;
		align-items: center;
	}
}
</style>
