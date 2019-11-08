<!-- Like.vue -->
<template>
	<div
		class="ui-like-wrapper"
		v-if="detailInfo && detailInfo.pintuan_type==1"
	>
		<div class="explain">
			<h2>此商品正在拼团</h2>
			<span class="desc">每人最多购买{{detailInfo.pintuan_limit_quantity}}件</span>
			<span class="desc">{{detailInfo.pintuan_limit_hour}}小时之内成团</span>
			<span class="desc">成团人数{{detailInfo.pintuan_limit_number}}人</span>
		</div>
		<!--<div v-for="(item,index) in detailInfo.pintuangroup_list" :key="index">-->
		<!--<div class="ui-detail-common">-->
			<!--<div class="header">-->
				<!--<span>点赞</span> <span>1</span>-->
			<!--</div>-->
		<!--</div>-->

		<!--<div class="like-body">-->
			<!--<div-->
				<!--v-for="(item, index) in detailInfo.collector"-->
				<!--v-if="index < 10"-->
				<!--v-bind:key="index"-->
			<!--&gt;-->
				<!--<img :src="item.thumb" v-if="item.thumb" />-->

			<!--</div>-->
		<!--</div>-->
	<!--</div>-->
	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {}
  },

  mounted () {},

  created () {},

  computed: {
    ...mapState({
      detailInfo: state => state.goodsdetail.detailInfo
    })
  }
}
</script>

<style lang="scss" scoped>
.ui-like-wrapper {
	background-color: #ffffff;
	margin-top:0.4rem;
	.explain{padding:.5rem;
		h2{color:$primaryColor;font-size:.8rem;padding-bottom: .5rem;}
		.desc{font-size:.7rem;color:gray;padding-right:.5rem;margin-right:.5rem;border-right:1px solid #e1e1e1}
		.desc:last-child{padding-right:0;margin-right:0;border-right:0}
	}
	.header {
		border-bottom: 0.5px solid #e8eaed;
		span:first-child {
			font-size: 0.7rem;
			color: rgba(78, 84, 93, 1);
			line-height:1rem;
		}
		span:last-child {
			font-size: 0.65rem;
			color: #333;
			line-height:0.9rem;
		}
	}

}
</style>
