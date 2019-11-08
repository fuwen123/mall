<!-- Detailinfo.vue -->
<template>
	<div class="ui-detail-info" v-if="detailInfo">
		<div class="info-header ui-flex">
			<h3>{{ detailInfo.pgoods_name }}</h3>

		</div>

		<div class="price">
			<span class="current-price">{{ detailInfo.pgoods_points }}积分</span>
			<span class="old-price">￥{{ detailInfo.pgoods_price }}</span>
		</div>

	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'

export default {
  data () {
    return {

      isShowDesc: false // 商品简介是否显示更多
    }
  },

  computed: {
    ...mapState({
      detailInfo: state => state.pointsgoods.detailInfo,
      currentProductId: state => state.pointsgoods.currentProductId,
      user: state => state.member.info
    })
  },

  created () {
  },

  methods: {
    ...mapMutations({
      changeIndex: 'changeIndex'
    })

  }
}
</script>

<style lang="scss" scoped>
.ui-detail-info {
	padding: 0 0.75rem;
	background: #ffffff;
	.ui-flex {
		display: flex;
		justify-content: space-between;
		align-content: center;
		align-items: center;
		flex-basis: 100%;
		width: auto;
	}
	.info-header {
		padding: 0.4rem 0;
		h3 {
			color: #333;
			font-size: 0.8rem;
			padding: 0;
			margin: 0;
			font-weight: normal;
		}
		div {
			display: flex;
			justify-content: space-between;
			align-content: center;
			align-items: center;
			img {
				width: 1.2rem;
				height: 1.2rem;
				vertical-align: middle;
				margin-left: 0.75rem;
				flex-shrink: 0;
			}
		}
		span {
			width: 1.5rem;
			height: 0.9rem;
			line-height: 0.9rem;
			text-align: center;
			background: $primaryColor;
			border-radius: 0.1rem;
			font-size: 0.6rem;
			color: rgba(255, 255, 255, 1);
			display: inline-block;
			margin-right: 0.5rem;
		}
	}

	.price {
		padding-bottom: 0.4rem;
		display: flex;
		span {
			display: block;
			font-weight: normal;
			&.current-price {
				font-size: 0.8rem;
				color: $primaryColor;
				line-height: 1rem;
			}
			&.old-price {
				font-size: 0.6rem;
				color: rgba(164, 170, 179, 1);
				line-height: 1rem;
				text-decoration: line-through;
				margin-left: 0.3rem;
			}
		}
	}

	.info-sub {
		border-bottom: 0.5px solid #e8eaed;
		padding-bottom: 0.75rem;
		p {
			padding: 0;
			margin: 0;
			color: $primaryColor;
			font-size: 0.6rem;
			&.ui-clip {
				display: -webkit-box;
				overflow: hidden;
			}
		}
		img {
			width: 0.4rem;
			height: 0.2rem;
			margin-left: 0.5rem;
		}
	}

}
</style>
