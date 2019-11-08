<!-- Concat.vue -->
<template>
	<div class="ui-concat-wrapper ui-detail-common" v-if="detailInfo">
		<div class="ui-promotions-wrapper ui-detail-common" @click="$router.push({name:'HomeGoodsConsult',query:{goods_id:detailInfo.goods_id}})">
			<!-- header -->
			<div class="promotions-header header">
				<h3>商品咨询<span class="iconfont">&#xe6ef;</span></h3>
			</div>

		</div>
	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {}
  },

  computed: {
    ...mapState({
      detailInfo: state => state.goodsdetail.detailInfo,
      user: state => state.member.info,
      isOnline: state => state.member.isOnline
    })
  },

  watch: {},

  mounted () {
    this.$nextTick(() => {})
  },

  created () {},

  methods: {
    goSingin () {
      this.$router.push({ name: 'HomeMemberLogin' })
    }
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

	}

</style>
