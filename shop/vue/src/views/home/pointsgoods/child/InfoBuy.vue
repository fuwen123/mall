<!-- Buy.vue -->
<template>
	<div class="ui-buy-wrapper ui-detail-common" v-if="detailInfo">
		<div
			class="buy-wrapper header"
			@click="changeCartState()"
			v-if="this.activeBuy()"
		>
			<p v-if="number <= 0">
				请选择购买数量分类
			</p>

			<p v-if="number > 0">
				已选数量{{ number }}
			</p>

			<span class="iconfont">&#xe6ef;</span>
		</div>
		<div class="buy-wrapper header isopacity" v-if="!this.activeBuy()">
			<p v-if="number <= 0">
				请选择购买数量分类
			</p>

			<p v-if="number > 0">
				已选数量{{ number }}
			</p>

			<span class="iconfont">&#xe6ef;</span>
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
      number: state => state.pointsgoods.number,
      detailInfo: state => state.pointsgoods.detailInfo
    })
  },

  created () {},

  methods: {
    ...mapMutations({
      saveCartState: 'saveCartState',
      changeType: 'changeType'
    }),

    activeBuy: function () {
      if (this.detailInfo.pgoods_storage > 0) {
        return true
      } else {
        return false
      }
    },

    changeCartState () {
      this.saveCartState(true)
      this.changeType('加入购物车')
    }

  }
}
</script>

<style lang="scss" scoped>
.ui-buy-wrapper {
	.buy-wrapper {
		&.header {
			padding: 0;
			height: 2rem;
		}
		&.isopacity {
			opacity: 0.5;
		}
		p {
			font-size: 0.7rem;
			color: rgba(78, 84, 93, 1);
			line-height: 1rem;
			padding: 0;
			margin: 0;
			i {
				font-weight: normal;
				font-style: normal;
			}
		}
		.iconfont {
			width:0.25rem;
			height: 0.5rem;
			cursor: pointer;
		}
	}
}
.ui-detail-common {
	height: 2rem;
	margin-top: 0.4rem;
	background: white;
	padding: 0 0.75rem;
}
.ui-detail-common .header {
	height: 100%;
	display: flex;
	justify-content: space-between;
	align-content: center;
	align-items: center;
	width: auto;
}
</style>
