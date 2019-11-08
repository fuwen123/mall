<!-- promotions.vue -->

<template>
	<div>
	<div
		class="ui-promotions-wrapper ui-detail-common"
		v-if="mansong_info"
	>

		<!-- 展示促销信息 -->
		<div class="promotions-body">
			<div
				class="body-list"
				v-for="(item, index) in mansong_info.rules"
				:key="index"
			>
				<span class="iconfont">&#xe631;</span>
				<span>满{{ item.mansongrule_price }}立减现金{{ item.mansongrule_discount }}</span>
			</div>
		</div>
	</div>
		<div class="ui-promotions-wrapper ui-detail-common" v-if="voucher" @click="$router.push({name:'HomeStoreVoucher'})">
			<!-- header -->
			<div class="promotions-header header">
			<h3>领取优惠券<span class="iconfont">&#xe6ef;</span></h3>
			</div>

		</div>

	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import { Toast } from 'mint-ui'

export default {
  data () {
    return {
      voucherVisible: false
    }
  },

  computed: {
    ...mapState({
      mansong_info: state => state.goodsdetail.mansongInfo,
		voucher: state => state.goodsdetail.voucher
    })
  },

  methods: {

  }
}
</script>

<style lang="scss" scoped>
.ui-promotions-wrapper {
	height: auto;
	&.ui-detail-common {
		padding: 0;
	}
	.promotions-header {
		padding: 0 0.75rem;
		margin-top: 0.4rem;
		height: 2rem;
		line-height: 2rem;
		background:#fff;
		h3 {
			font-size: 0.7rem;
			color: #333;
			.iconfont{float:right;margin-right:-0.55rem}
		}
		img {
			width:0.25rem;
			height: 0.5rem;
		}
	}
	.promotions-body {
		background: #ffffff;
		.body-list {
			border-top: 0.5px solid #e8eaed;
			padding: 0 0.75rem;
			height: 2.2rem;
			display: flex;
			flex-direction: row;
			align-items: center;
			img {
				width: 0.75rem;
				height: 0.75rem;
			}
			span {
				font-size: 0.7rem;
				color: #999999;
				margin-left: 0.5rem;
			}
		}
	}

}

</style>
