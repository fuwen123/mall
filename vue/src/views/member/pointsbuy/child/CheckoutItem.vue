<template>
    <div class="container" @click="onclick">
        <label class="title">{{title}}</label>
        <label class="tips" v-if="isShowTips">{{tips}}</label>
        <label class="subtitle">{{subtitle}}</label>
        <i class="indicator iconfont">&#xe650;</i>
    </div>
</template>

<script>
export default {
  props: {
    title: {
      type: String
    },
    subtitle: {
      type: String
    },
    tips: {
      type: String
    }
  },
  computed: {
    isShowTips: function () {
      if (this.tips && this.tips.length) {
        return true
      }
      return false
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
    .container {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
        background-color: #fff;
    }
    .title {
        width: auto;
        font-size:0.8rem;
        color: #333;
        margin-left:0.75rem;
    }
    .tips {
        width: auto;
        font-size:0.5rem;
        color: $primaryColor;
        margin-left:0.5rem;
        border: 1px solid #e93b3d;
        border-radius:0.1rem;
        padding-left:0.25rem;
        padding-right:0.25rem;
    }
    .subtitle {
        flex: 1;
        margin-left:1rem;
        color: #7C7F88;
        font-size:0.65rem;
        text-align: right;
    }
    .indicator {
        width:0.35rem;
        height:0.6rem;
        margin-left:0.5rem;
        margin-right:0.5rem;
    }
</style>
