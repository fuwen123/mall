<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header title="推广管理" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
      </mt-header>
    </div>
    <mt-field label="推广链接" type="text" :readonly="true" :value="inviter_url"></mt-field>
    <div @click='weixinVisible=true'><mt-cell class="menu-item" title="推广公众号" is-link></mt-cell></div>
    <div @click='posterVisible=true'><mt-cell class="menu-item" title="推广海报" is-link></mt-cell></div>
    <div @click='$router.push({name:"MemberInviterUser"})'><mt-cell class="menu-item" title="推广成员" is-link></mt-cell></div>
    <div @click='$router.push({name:"MemberInviterOrder"})'><mt-cell class="menu-item" title="推广业绩" is-link></mt-cell></div>
    <!--推广公众号-->
    <mt-popup v-model="weixinVisible" position="right" class="common-popup-wrapper">
      <div class="common-header-wrap">
        <mt-header title="推广公众号" class="common-header">
          <mt-button slot="left" icon="back" @click="weixinVisible=false"></mt-button>
        </mt-header>
      </div>
      <div class="common-popup-content">
        <div class="refer_qrcode_weixin" v-if="refer_qrcode_weixin" >
          <img :src="refer_qrcode_weixin">
          <p>赶快去推广微信二维码吧!</p>
        </div>
        <p class="refer_qrcode_weixin_error" v-else>{{wx_error_msg}}</p>
      </div>
    </mt-popup>
    <!--推广海报-->
    <mt-popup v-model="posterVisible" position="right" class="common-popup-wrapper">
      <div class="common-header-wrap">
        <mt-header title="推广海报" class="common-header">
          <mt-button slot="left" icon="back" @click="posterVisible=false"></mt-button>
        </mt-header>
      </div>
      <div class="common-popup-content">
        <img class="refer_qrcode_logo" :src="refer_qrcode_logo" :style="{width:wrapperWidth+'px'}">
      </div>
    </mt-popup>
  </div>
</template>

<script>
import { getInviterPoster } from '../../../api/memberInviter'
import { Toast } from 'mint-ui'
export default {
  components: {
  },
  data () {
    return {
      weixinVisible: false,
      posterVisible: false,
      inviter_url: '',
      refer_qrcode_logo: '',
      refer_qrcode_weixin: '',
      wx_error_msg: '',
      wrapperWidth: 0
    }
  },
  created () {
    getInviterPoster().then(res => {
      this.inviter_url = res.result.inviter_url
      this.refer_qrcode_logo = res.result.refer_qrcode_logo
      this.refer_qrcode_weixin = res.result.refer_qrcode_weixin
      this.wx_error_msg = res.result.wx_error_msg
    }).catch(function (error) {
      Toast(error.message)
    })
  },

  mounted () {
    this.wrapperWidth = document.documentElement.clientWidth - 20
  },
  methods: {

  }
}
</script>
<style scoped lang="scss">
  .inviter_url{font-size:.7rem;padding:.5rem;margin-left: .5rem;box-sizing:border-box}
  .refer_qrcode_weixin_error{font-size:.7rem;padding:.5rem;}
  .refer_qrcode_logo{margin-left: .5rem}
  .refer_qrcode_weixin{text-align: center;margin-top:30%;}
  .refer_qrcode_weixin img{max-width:50%}
  .refer_qrcode_weixin p{line-height:2rem;font-size:0.7rem;}
</style>
