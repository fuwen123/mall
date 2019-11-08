<!-- DetailHeader.vue -->
<template>
  <div>
    <div class="ui-detail-header">
      <span class="iconfont"
            @click="$router.go(-1)">&#xe6ee;</span>
      <div class="navbar-wrapper">
        <div v-for="(item, key) in data"
             :key="key"
             v-bind:class="{ navbar_active: key == index }"
             v-on:click="changeEvent(key)">
          {{ item.name }}
        </div>
      </div>
      <span class="iconfont right"
            @click="popupMore">&#xe680;<div v-if="showDot"
             class="dot"></div></span>
    </div>
    <header-more :goods_id="goods_id"
                 :share_info="share_config"
                 v-show="popupVisible"
                 :showDot="showDot"></header-more>
  </div>
</template>

<script>
import { header } from './static'
import { getChatCount } from '../../../api/memberChat'
import { mapState, mapMutations } from 'vuex'
import HeaderMore from '../../../components/HeaderMore'
export default {
  data () {
    return {
      share_config: false,
      data: header,
      popupVisible: false,
      showDot: false,
    }
  },
  components: {
    HeaderMore
  },
  computed: {
    ...mapState({
      detailInfo: state => state.goodsdetail.detailInfo,
      goods_id: state => state.goodsdetail.currentProductId,
      index: state => state.goodsdetail.index,
      isOnline: state => state.member.isOnline,
    })
  },
  created () {
    this.changeIndex(0)
    this.saveNumber(1)
    if (this.isOnline) {
      getChatCount().then(res => {
        if (res.result) {
          this.showDot['chat'] = true
        }
      })
    }
  },
  methods: {
    ...mapMutations({
      changeIndex: 'changeIndex',
      saveNumber: 'saveNumber'
    }),
    popupMore () {
      if (!this.popupVisible) {
        this.popupVisible = true
      } else {
        this.popupVisible = false
      }
    },
    changeEvent (index) {
      this.changeIndex(index)
    },
    goBack () {
      this.$router.go(-1)
    }
  },
  watch: {
    detailInfo: function (val) {
      if (val) {
        this.share_config = { title: val.goods_name, link: window.location.href, imgUrl: val.photos[0], desc: val.goods_advword }
      }
    }
  }
}
</script>

<style lang="scss" scoped>
.dot {
  position: absolute;
  width: 0.5rem;
  height: 0.5rem;
  background: red;
  border-radius: 50%;
  top: 0.2rem;
  right: 0;
}
.ui-detail-header {
  padding: 0 0.45rem;
  // height:3.2rem;
  background: rgba(255, 255, 255, 1);
  border-bottom: 1px solid rgba(232, 234, 237, 1);
  color: #55595f;
  font-size: 0.7rem;
  width: auto;
  display: flex;
  justify-content: center;
  align-content: center;
  align-items: center;
  flex-basis: auto;
  position: absolute;
  top: 0;
  left: 0;
  right: 0;
  .iconfont {
    width: 1.2rem;
    font-size: 0.7rem;
    height: 1.2rem;
    line-height: 1.2rem;
    cursor: pointer;
    position: absolute;
    left: 0.45rem;
    top: 0.5rem;
  }
  .iconfont.right {
    left: auto;
    right: 0.45rem;
  }
  div.navbar-wrapper {
    line-height: 2.1rem;
    div {
      // line-height: 2.1rem;
      border-bottom: 0;
      display: inline-block;
      margin-right: 2.4rem;
      color: #55595f;
      background-color: #fff;
      &.navbar_active {
        color: $primaryColor;
        border-bottom: 0.1rem solid $primaryColor;
      }
      &:last-child {
        margin-right: 0;
      }
      &:focus {
        outline: none;
      }
    }
  }
}
</style>
