<template>
    <div class="container" @click="onclick">
        <div class="top-wrapper">
            <div v-if="hasAddress" class="selected-wrapper">
                <div class="title-wrapper">
                    <i class="icon iconfont">&#xe6d3;</i>
                    <label class="title">{{item.address_realname}}</label>
                    <label class="title">{{item.address_mob_phone}}</label>
                    <label class="default" v-if="isDefault">默认</label>
                </div>
                <label class="desc address-text" style="-webkit-box-orient:vertical">{{item.area_info}}{{item.address_detail}}</label>
            </div>
            <div v-else class="unselected-wrapper">
                <label class="desc">您还没有收获地址，点击这里添加。</label>
            </div>
            <i class="indicator iconfont">&#xe650;</i>
        </div>
        <div class="line-wrapper"></div>
    </div>
</template>

<script>
export default {
  props: ['item'],
  created: function () {
  },
  computed: {
    hasAddress () {
      if (this.item) {
        return true
      }
      return false
    },
    isDefault () {
      if (this.item && this.item.is_default) {
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
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        background-color: #fff;
    }
    .top-wrapper {
        flex: 1;
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
        background-color: #fff;
    }
    .selected-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
    }
    .title-wrapper {
        height:1rem;
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
    }
    .icon {
        width:0.8rem;
        height:0.8rem;
        margin-left:0.5rem;
    }
    .title {
        font-size:0.8rem;
        color: #333;
        margin-left:0.5rem;
    }
    .default {
        width:1.4rem;
        margin-left: 0.5rem;
        margin-right:0.5rem;
        border: 1px solid #e93b3d;
        color: $primaryColor;
        font-size:0.5rem;
        text-align: center;
        border-radius:0.1rem;
    }
    .desc {
        color: #7C7F88;
        font-size:0.7rem;
    }
    .address-text {
        margin-top:0.5rem;
        margin-bottom:0.5rem;
        margin-left:1.8rem;
    }
    .unselected-wrapper {
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: center;
        align-items: center;
    }
    .indicator {
        width:0.35rem;
        height: 0.6rem;
        margin-left:0.5rem;
        margin-right:0.5rem;
    }
    .line-wrapper {
        background:#eee;
        left: 0;
        right: 0;
        bottom: 0;
        width: 100%;
        height: 0.1rem;
    }
</style>
