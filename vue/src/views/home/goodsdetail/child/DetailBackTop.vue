<!-- DetailBackTop.vue -->
<template>
	<div class="detail-back-top ">
		<div v-if="!isTop">
			<span class="iconfont" @click="goBackTop()">&#xe65c;</span>
		</div>
	</div>
</template>

<script>
export default {
  data () {
    return {
      isTop: false
    }
  },

  props: {
    target: {}
  },

  created () {
    // this.srollertop = document.documentElement.scrollTop || document.body.scrollTop;
    var that = this
    this.target.addEventListener('scroll', event => {
      if (this.target.scrollTop || this.target.scrollTop >= 100) {
        that.isTop = false
      } else {
        that.isTop = true
      }
    })
  },
  mounted () {
  },
  methods: {
    /*
		 *  goBackTop： 回到顶部
		 */
    goBackTop () {
      this.target.scrollTop = 0
      this.isTop = true
    }
  }
}
</script>

<style lang="scss" scoped>
.detail-back-top {
	position: fixed;
	bottom:3rem;
	width:2.5rem;
	height:2.5rem;
	right:0.5rem;
	div {
		width: 100%;
		height: 100%;
		img {
			width:2.5rem;
			height:2.5rem;
		}
	}
}
</style>
