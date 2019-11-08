<template>
  <div class="page-loadmore-wrapper">
    <mt-loadmore :auto-fill='false' :bottom-method="getMore" @bottom-status-change="handleBottomChange" :bottom-all-loaded="allLoaded" ref="mt_loadmore">
      <slot></slot>
        <div slot="bottom" class="mint-loadmore-bottom">
          <span v-show="bottomStatus === 'loading'">
            <mt-spinner type="snake"></mt-spinner>
          </span>
        </div>
    </mt-loadmore>
  </div>
</template>

<script>

export default {
  name: 'LoadMore',
  data () {
    return {
      bottomStatus: '',
      allLoaded: false
    }
  },
  props: {

  },
  methods: {
    getMore () {
      this.$emit('getMore')
    },
    handleBottomChange (status) {
      this.bottomStatus = status
    }
  }
}
</script>
