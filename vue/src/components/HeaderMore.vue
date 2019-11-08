<template>
  <div class="more-box">
    <div class="more-item"
         @click="$router.push({name:'HomeIndex'})"><i class="iconfont">&#xe6b2;</i>首页</div>
    <div class="more-item"
         @click="$router.push({name:'MemberInformForm',query:{goods_id:goods_id}})"
         v-if="routerName === 'HomeGoodsdetail'"><i class="iconfont">&#xe6a3;</i>违规举报</div>
    <div class="more-item"
         @click="$router.push({name:'HomeSearch'})"><i class="iconfont">&#xe61f;</i>搜索</div>
    <div class="more-item"
         @click="$router.push({name:'HomeCart'})"><i class="iconfont">&#xe6ae;</i>购物车</div>
    <div class="more-item"
         v-if="config && config.node_site_use == '1' && config.node_site_url"
         @click="$router.push({name:'MemberChatList'})"><i class="iconfont">&#xe67c;</i>聊天消息<div v-if="showDot && showDot.chat"
           class="dot"></div>
    </div>
    <div class="more-item"
         @click="$router.push({name:'MemberIndex'})"><i class="iconfont">&#xe6b4;</i>我的商城</div>
    <div class="more-item"
         @click="onShare"><i class="iconfont">&#xe625;</i>分享</div>
    <i class="arrow"></i>

    <mt-popup v-model="shareVisible"
              position="bottom"
              class="common-popup-wrapper">
      <div class="common-header-wrap">
        <mt-header title="分享至"
                   class="common-header">
          <mt-button slot="left"
                     icon="back"
                     @click="shareVisible=false"></mt-button>
        </mt-header>
      </div>
      <div class="common-popup-content">
        <div class="share-list">
          <div class="share-item weixin"
               @click="weixinShareImage=true">微信</div>
          <div class="share-item copy"
               @click="showLink">复制</div>
        </div>
      </div>
      <div class="weixin-share-wrapper"
           :style="getWrapperStyle"
           v-if="weixinShareImage"
           @click="weixinShareImage=false"><img class="weixin-share"
             src="../assets/image/weixin-share.png"></div>
    </mt-popup>

    <mt-popup v-model="copyVisible"
              class="copy-wrapper">
      <div class="title">您的浏览器不支持直接复制，请手动复制</div>
      <input type="text"
             :value="copyLink"
             onfocus="this.select()">

    </mt-popup>
  </div>
</template>

<script>
import { Toast } from 'mint-ui'
import { getWechatShare } from '../api/common'
import { loadScript } from '../util/common'
import { isWechat } from '../util/wechat'
import { mapState, mapMutations, mapActions } from 'vuex'
export default {
  name: 'HeaderMore',
  data () {
    return {
      shareVisible: false,
      weixinShareImage: false,
      copyVisible: false,
      copyLink: process.env.VUE_APP_H5_HOST + this.$route.fullPath
    }
  },
  components: {
  },
  created: function () {

  },
  props: {
    goods_id: {},
    share_info: {},
    showDot: {}
  },
  computed: {
    ...mapState({
      config: state => state.config.config
    }),
    routerName () {
      return this.$route.name
    },
    getWrapperStyle: function () {
      const { width, height } = window.screen
      return {
        width: width + 'px',
        height: height + 'px'
      }
    }
  },
  watch: {
    share_info: function (val) {
      if (val && isWechat() && this.config.wechat_appid) {
        this.setWechat(val)
      }
    }
  },
  methods: {
    showLink () {
      if (window.clipboardData) {
        window.clipboardData.clearData()
        window.clipboardData.setData('Text', this.copyLink)
        Toast('复制成功！')
      } else {
        this.copyVisible = true
      }
    },
    onShare () {
      this.shareVisible = true
    },
    setWechat (share_info) {
      getWechatShare(encodeURIComponent(window.location.href)).then(res => {
        loadScript('weixin', 'https://res.wx.qq.com/open/js/jweixin-1.3.2.js', function () {
          wx.config({
            debug: false,
            appId: res.result.signPackage.appId,
            timestamp: res.result.signPackage.timestamp,
            nonceStr: res.result.signPackage.nonceStr,
            signature: res.result.signPackage.signature,
            jsApiList: [
              // 所有要调用的 API 都要加到这个列表中
              'onMenuShareTimeline',
              'onMenuShareAppMessage',
              'onMenuShareQQ',
              'onMenuShareWeibo',
              'onMenuShareQZone'
            ]
          })
          wx.ready(function () {
            wx.onMenuShareAppMessage({
              title: share_info.title,
              link: share_info.link,
              imgUrl: share_info.imgUrl,
              desc: share_info.desc // 分享描述
            })

            wx.onMenuShareTimeline({
              title: share_info.title,
              link: share_info.link,
              imgUrl: share_info.imgUrl,
              desc: share_info.desc
            })
            wx.onMenuShareQQ({
              title: share_info.title, // 分享标题
              desc: share_info.desc, // 分享描述
              link: share_info.link, // 分享链接
              imgUrl: share_info.imgUrl // 分享图标

            })
            wx.onMenuShareWeibo({
              title: share_info.title, // 分享标题
              desc: share_info.desc, // 分享描述
              link: share_info.link, // 分享链接
              imgUrl: share_info.imgUrl // 分享图标

            })
            wx.onMenuShareQZone({
              title: share_info.title, // 分享标题
              desc: share_info.desc, // 分享描述
              link: share_info.link, // 分享链接
              imgUrl: share_info.imgUrl // 分享图标

            })
          })
          wx.error(function (res) {
          })
        })
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  }
}
</script>

<style scoped>
.more-box {
  opacity: 0.9;
  background: #000;
  position: absolute;
  width: 5.6rem;
  z-index: 1;
  right: 0.3rem;
  top: 2.2rem;
  border-radius: 0.2rem;
}
.more-item {
  border-bottom: 1px solid hsla(0, 0%, 100%, 0.2);
  height: 2rem;
  line-height: 2rem;
  list-style: none outside none;
  color: #fff;
  font-size: 0.7rem;
  position: relative;
}
.more-item i {
  margin: 0 0.4rem;
}
.more-item .dot {
  position: absolute;
  width: 0.5rem;
  height: 0.5rem;
  background: red;
  border-radius: 50%;
  top: 0.5rem;
  left: 1rem;
}
.arrow {
  position: absolute;
  display: block;
  right: 0.2rem;
  top: -0.7rem;
  width: 0;
  height: 0;
  margin-right: 0.45rem;
  font-size: 0;
  line-height: 0;
  border-width: 0.4rem;
  border-color: transparent transparent rgb(0, 0, 0) transparent;
  border-style: dashed dashed solid dashed;
}
.common-popup-wrapper {
  height: 10rem;
  overflow: visible;
}
.more-box .share-list {
  display: flex;
}
.more-box .share-list .share-item {
  height: 3rem;
  margin: 1rem 0.8rem;
  width: 2.5rem;
  padding-left: 0;
  background: none;
  text-align: center;
  color: #333;
  font-size: 0.6rem;
}
.more-box .share-list .share-item:before {
  font-family: "iconfont";
  content: "";
  display: block;
  border-radius: 50%;
  width: 2rem;
  height: 2rem;
  line-height: 2rem;
  text-align: center;
  font-size: 1.2rem;
  color: #fff;
  background: red;
  margin: 0 auto;
  margin-bottom: 0.25rem;
}
.more-box .share-list .share-item.weixin:before {
  content: "\e647";
  background: #c71585;
}
.more-box .share-list .share-item.copy:before {
  content: "\e64b";
  background: #228b22;
}
.more-box .weixin-share-wrapper {
  position: absolute;
  bottom: 0;
  left: 0;
  text-align: right;
}
.more-box .weixin-share {
  height: 10rem;
}
.copy-wrapper {
  padding: 1rem;
}
.copy-wrapper input {
  height: 1.5rem;
  line-height: 1.5rem;
  padding: 0 0.2rem;
  width: 12rem;
  border: 1px solid #e1e1e1;
}
.copy-wrapper .title {
  font-size: 0.6rem;
  text-align: center;
  margin-bottom: 0.5rem;
}
</style>
