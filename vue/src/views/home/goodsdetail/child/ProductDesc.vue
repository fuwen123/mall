<template>
	<div class="ui-detail">
		<div>
			<desc-item
				:mbBody="mbBody"
				:detailInfo="detailInfo"
				v-if="detailInfo"
			></desc-item>
		</div>
		<v-back-top v-if="isshowBacktop" :target="target"></v-back-top>
	</div>
</template>

<script>
import { mapState } from 'vuex'
import DescItem from './DescItem'
import BackTop from './DetailBackTop'
export default {
  data () {
    return {
      isshowBacktop: false
    }
  },

  mounted () {
    // 添加滚动事件
    var element = this.$el
    element.addEventListener('scroll', event => {
      let params = {
        top: element.scrollTop,
        height: element.scrollHeight
      }
      if (params.top >= 100) {
        this.isshowBacktop = true
      } else {
        this.isshowBacktop = false
      }
      // if( params.height - (params.top + element.offsetHeight + 2) <  0) {
      // 	this.isshowBacktop = true;
      // } else {
      // 	this.isshowBacktop = false;
      // }
    })

    // 计算内容高度
    this.$nextTick(() => {
      this.target = document.querySelector('.ui-detail')
    })
  },

  components: {
    'desc-item': DescItem,
    'v-back-top': BackTop
  },

  computed: mapState({
    detailInfo: state => state.goodsdetail.detailInfo,
    mbBody: state => state.goodsdetail.mbBody
  }),

  methods: {}
}
</script>

<style lang="scss" scoped>
.ui-detail {
	height: 100%;
	overflow: auto;
}
</style>
