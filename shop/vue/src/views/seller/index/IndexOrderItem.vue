<template>
    <div @click="onclick()">
        <i class="order-item-icon iconfont" v-html="iconfont"></i>
        <label class="item-title order-item-title">{{title}}</label>
        <span class="number" v-if="orderNumber == 0 ? '': orderNumber && isEmpty == false ? '': orderNumber">{{ orderNumber }}</span>
    </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'

export default {
  props: {
    iconfont: {
      type: String
    },
    title: {
      type: String
    },
    testAttr: {
      type: String
    },
    id: {
      default: ''
    },
    orderNumber: {
      type: Number,
      default: 0
    }
  },
  data () {
    return {
      isEmpty: false
    }
  },
  computed: mapState({
    isOnline: state => state.member.isOnline
  }),
  created () {
    this.isSignin()
  },
  methods: {
    ...mapMutations({
      changeStatus: 'changeStatus'
    }),
    onclick () {
      if (this.isOnline) {
        this.$router.push({ name: this.testAttr, query: { state: this.id } })
      } else {
        this.$router.push({ name: 'signin' })
      }
    },
    // 是否登录
    isSignin () {
      if (this.isOnline) {
        this.isEmpty = true
      } else {
        this.isEmpty = false
      }
    }
  }
}
</script>

<style lang="scss" scoped>
    .order-item {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: center;
    }
    .item-title {
        font-size: 0.7rem;
        color: #333;
    }
    .order-item-icon {
        width: 1.4rem;
        height: 1.4rem;
        font-size:1.3rem;
        margin-top:0.8rem;
    }
    .order-item-title {
        margin-top:0.4rem;
    }
    span.number {
        width:0.8rem;
        height:0.8rem;
        line-height:0.8rem;
        margin-top:-2.8rem;
        margin-left:0.8rem;
        background: #e93b3d;
        border-radius:50%;
        font-size:0.5rem;
        text-align: center;
        color: RGBA(255, 255, 255, 1);
        font-weight: normal;
    }
</style>
