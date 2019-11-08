<template>
  <div class="info-item-wrapper pt-5 pb-5" @click="onclick">
    <i class="info-item-icon iconfont"  v-html="iconfont"></i>
    <label class="item-title info-item-title">{{title}}</label>
    <i class="indicator iconfont">&#xe650;</i>
  </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String
    },
    iconfont: {
      type: String
    }
  },
  methods: {
    onclick () {
      this.$emit('onclick')
    }
  }
}
</script>

<style lang="scss" scoped>
  .info-item-wrapper {
    display: flex;
    background-color: #fff;
    height:1rem;
    line-height:1rem;
  }
  .item-title {
    font-size:0.6rem;
    color: #666;
  }
  .info-item-icon {
    width:1rem;
    height:1rem;
    text-align:center;
    margin-left:0.5rem;
    font-size:0.8rem;
  }
  .info-item-title {
    flex: 1;
    margin-left:.2rem;
  }
  .indicator {
    width:0.4rem;
    margin-left:0.5rem;
    margin-right:1rem;
  }
</style>
