<template>
	<div class="ui-detail">
		<div>
			<desc-item
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
    var that = this
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
    detailInfo: state => state.pointsgoods.detailInfo,

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
