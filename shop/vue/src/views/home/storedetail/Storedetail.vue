<template>
  <div class="container">

    <div class="header"
         :style="{backgroundImage:'url('+store.mb_title_img+')'}">
      <div class="content-wrapper">
        <div class="avatar"
             @click="goStoreabout">
          <img :src="store.store_avatar">
        </div>
        <div class="content">
          <div class="store_name">{{store.store_name}}</div>
          <div v-if="(store.is_platform_store && config.business_licence) || store.business_licence_number_electronic"
               @click="imageVisible=true"><i class="iconfont">&#xe621;</i></div>
        </div>
        <div class="follow_panel">
          <div class="follow_button"
               @click="toggleFavorite">
            <i class="iconfont"
               :class="{'active':store.is_favorate}"
               v-html="favoriteIco"></i>
            {{favoriteName}}
          </div>
          <div class="follow_number">
            {{store.store_collect}}人收藏
          </div>
        </div>
        <i class="header-more iconfont"
           @click="popupMore">&#xe680;<div v-if="showDot"
               class="dot"></div></i>
      </div>
      <div class="background">
        <img v-if="store.store_logo"
             v-lazy="store.store_logo"
             width="100%"
             height="100%">
        <img v-else
             v-lazy="store.store_logo"
             width="100%"
             height="100%">
      </div>
    </div>
    <header-more v-show="popupVisible"
                 :showDot="showDot"></header-more>
    <mt-swipe class="mt-5"
              v-if="store.mb_sliders && store.mb_sliders.length"
              v-bind:style="getBannerStyle"
              :showIndicators="isShowIndicators">
      <mt-swipe-item v-for="(item, index) in store.mb_sliders"
                     :key="index">
        <img :style="getBannerStyle"
             v-lazy="item.imgUrl"
             @click="goAd(item)">
      </mt-swipe-item>
    </mt-swipe>
    <store-product-list :items="rec_goods_list"
                        v-if="rec_goods_list && rec_goods_list.length > 0"></store-product-list>
    <common-store-footer :store_id="store.store_id"></common-store-footer>
    <mt-popup v-model="imageVisible"
              popup-transition="popup-fade"
              class="middle-popup">
      <img v-if="store.business_licence_number_electronic"
           :src="store.business_licence_number_electronic">
      <img v-if="store.is_platform_store && config.business_licence"
           :src="config.business_licence">
    </mt-popup>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { Toast } from 'mint-ui'
import CommonStoreFooter from '../common/CommonStoreFooter'
import { getStoreInfo } from '../../../api/homestoredetail'
import { getChatCount } from '../../../api/memberChat'
import { addFavoriteStore, delFavoriteStore } from '../../../api/memberFavorite'
import StoreProductList from './StoreProductList'
import HeaderMore from '../../../components/HeaderMore'
export default {
  name: 'Storedetail',
  data () {
    return {
      imageVisible: false,
      store: {
        store_id: this.$route.query.id ? this.$route.query.id : ''
      },
      rec_goods_list: false,
      popupVisible: false, // 弹出更多
      showDot: false,
    }
  },
  components: {
    HeaderMore,
    StoreProductList,
    CommonStoreFooter
  },
  computed: {
    ...mapState({
      config: state => state.config.config,
      token: state => state.member.token,
      isOnline: state => state.member.isOnline,
    }),
    getBannerStyle: function () {
      const { width } = window.screen
      let itemWidth = width
      let itemHeight = width * (94.0 / 188.0)
      return {
        width: itemWidth + 'px',
        height: itemHeight + 'px'
      }
    },
    isShowIndicators () {
      if (this.store.mb_sliders && this.store.mb_sliders.length > 1) {
        return true
      }
      return false
    },
    favoriteIco () {
      return this.store.is_favorate ? '&#xe64d;' : '&#xe64e;'
    },
    favoriteName () {
      return this.store.is_favorate ? '已收藏' : '收藏'
    }
  },
  created () {
    getStoreInfo(
      this.store.store_id, this.token
    ).then((res) => {
      this.store = res.result.store_info
      this.rec_goods_list = res.result.rec_goods_list
      if (this.store.is_platform_store) {
        this.fetchConfig({}).then(
          response => {
          },
          error => {
            Toast(error.message)
          }
        )
      }
    }).catch(function (error) {
    })
    if (this.isOnline) {
      getChatCount().then(res => {
        if (res.result) {
          this.showDot['chat'] = true
        }
      })
    }
  },
  methods: {
    ...mapActions({
      fetchConfig: 'fetchConfig'
    }),
    goAd (item) {
      if (item.type === 2) {
        this.$router.push({ name: 'HomeGoodsdetail', query: { goods_id: item.link } })
      } else {
        window.location.href = item.link
      }
    },
    goStoreabout: function () {
      this.$router.push({ name: 'HomeStoreabout', query: { id: this.$route.query.id } })
    },
    // 弹出更多
    popupMore () {
      if (!this.popupVisible) {
        this.popupVisible = true
      } else {
        this.popupVisible = false
      }
    },
    toggleFavorite () {
      if (!this.store.is_favorate) {
        addFavoriteStore(this.store.store_id).then(
          response => {
            Toast(response.message)
            this.store.is_favorate = !this.store.is_favorate
          },
          error => {
            Toast(error.message)
          }
        )
      } else {
        delFavoriteStore(this.store.store_id).then(
          response => {
            Toast(response.message)
            this.store.is_favorate = !this.store.is_favorate
          },
          error => {
            Toast(error.message)
          }
        )
      }
    }
  }

}
</script>

<style scoped>
.dot {
  position: absolute;
  width: 0.5rem;
  height: 0.5rem;
  background: red;
  border-radius: 50%;
  top: 0.2rem;
  right: 0;
}
.header {
  background: rgba(7, 17, 27, 0.5);
  color: #fff;
  overflow: hidden;
  position: relative;
  background-position: center;
  background-size: auto 100%;
}
.header .content-wrapper {
  -ms-flex-align: center;
  -webkit-box-align: center;
  align-items: center;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  padding: 0.8rem 0.5rem;
  position: relative;
}
.header .content-wrapper .avatar {
  -ms-flex: 0 0 3rem;
  -webkit-box-flex: 0;
  flex: 0 0 3rem;
  margin-right: 0.4rem;
  width: 3rem;
}
.header .content-wrapper .avatar img {
  border-radius: 0.1rem;
  width: 3rem;
  height: 3rem;
}
.header .content-wrapper .content {
  position: relative;
  width: 8rem;
}
.header .content-wrapper .content .store_name {
  font-size: 0.8rem;
  font-weight: 700;
  margin-bottom: 0.4rem;
}
.follow_panel {
  position: absolute;
  top: 1rem;
  right: 2rem;
}
.follow_panel .follow_button {
  display: block;
  background: #e93b3d;
  border-radius: 2.5rem;
  padding: 0 0.4rem;
  line-height: 1.2rem;
  font-size: 0.6rem;
  color: #fff;
}
.follow_panel .follow_button i {
  line-height: 1.2rem;
}
.follow_panel .follow_number {
  text-align: center;
  line-height: 0.7rem;
  font-size: 0.5rem;
  color: #fff;
  margin-top: 0.1rem;
}
.header-more {
  width: 2rem;
  height: 2rem;
  line-height: 2rem;
  position: absolute;
  top: 0.5rem;
  right: 0;
  color: #fff;
  text-align: center;
  font-size: 1rem;
}
.header .background {
  background: #2c3b53;
  -webkit-filter: blur(0.5rem);
  filter: blur(0.5rem);
  height: 100%;
  left: 0;
  position: absolute;
  top: 0;
  width: 100%;
  z-index: -1;
}
.middle-popup {
  width: 80%;
}
.middle-popup img {
  max-width: 100%;
}
</style>
