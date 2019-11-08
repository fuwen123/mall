<template>
    <div @click="onclick()" class="item">
        <i class="item-icon iconfont" :style="{ color: color }" v-html="iconfont"></i>
        <span class="item-title">{{title}}</span>
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
    },
    color: {
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
    .item {
        padding:.5rem 0;
        text-align: center;
        height:3rem;
        box-sizing: border-box;
        float: left;
        width: 25%;
        position: relative;
    }
    .item-icon {
        display: block;
        margin: 0 auto;
        width:1rem;
        height:1rem;
        line-height:1rem;
        font-size:1.1rem;
        font-weight:500;
    }
    .item-title {
        display:block;
        font-size:0.6rem;
        color: #666;
        height:1rem;
        line-height:1rem;
        margin-top:0.3rem;
    }
</style>
