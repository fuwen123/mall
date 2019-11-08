<!-- Buy.vue -->
<template>
	<div class="ui-buy-wrapper ui-detail-common" v-if="detailInfo">
		<div
			class="buy-wrapper header"
			@click="changeCartState()"
			v-if="this.activeBuy()"
		>
			<p v-if="number <= 0 && chooseinfo.ids.length <= 0">
				请选择购买{{ chooseinfo.specification.join(',') }}数量分类
			</p>
			<p v-if="number <= 0 && chooseinfo.ids.length > 0">
				已选{{ chooseinfo.specification.join(',') }},数量{{ number + 1 }}
			</p>
			<p v-if="number > 0 && chooseinfo.ids.length <= 0">
				已选数量{{ number }}
			</p>
			<p v-if="number > 0 && chooseinfo.ids.length > 0">
				已选{{ chooseinfo.specification.join(',') }},数量{{ number }}
			</p>
			<span class="iconfont">&#xe6ef;</span>
		</div>
		<div class="buy-wrapper header isopacity" v-if="!this.activeBuy()">
			<p v-if="number <= 0 && chooseinfo.ids.length <= 0">
				请选择购买{{ chooseinfo.specification.join(',') }}数量分类
			</p>
			<p v-if="number <= 0 && chooseinfo.ids.length > 0">
				已选{{ chooseinfo.specification.join(',') }},数量{{ number + 1 }}
			</p>
			<p v-if="number > 0 && chooseinfo.ids.length <= 0">
				已选数量{{ number }}
			</p>
			<p v-if="number > 0 && chooseinfo.ids.length > 0">
				已选{{ chooseinfo.specification.join(',') }},数量{{ number }}
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
      number: state => state.goodsdetail.number,
      detailInfo: state => state.goodsdetail.detailInfo,
      chooseinfo: state => state.goodsdetail.chooseinfo
    })
  },

  created () {},

  watch: {
    detailInfo: function (value) {
      this.setSpecification()
    }
  },

  methods: {
    ...mapMutations({
      saveCartState: 'saveCartState',
      saveChooseInfo: 'saveChooseInfo',
      changeType: 'changeType'
    }),

    activeBuy: function () {
      if (this.detailInfo.goods_storage > 0) {
        return true
      } else {
        return false
      }
    },

    changeCartState () {
      this.saveCartState(true)
      this.changeType('加入购物车')
    },

    setSpecification () {
      if (this.detailInfo && this.detailInfo.properties) {
        let data = this.detailInfo.properties
        let arrays = []
        for (let i = 0; i <= data.length - 1; i++) {
          arrays.push(data[i].name)
        }
        this.saveChooseInfo({ specification: arrays, ids: [] })
      }
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
