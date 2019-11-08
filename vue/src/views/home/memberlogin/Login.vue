<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header class="common-header"
                 title="登录">
        <mt-button icon="back"
                   slot="left"
                   @click="$router.go(-1)"></mt-button>
        <mt-button slot="right"
                   @click="onSignup">注册</mt-button>
      </mt-header>
    </div>

    <div class="top-wrapper">
      <mt-field class="input-wrapper"
                v-model="username"
                placeholder="用户名/邮箱/手机号"
                type="text">
      </mt-field>
      <mt-field class="input-wrapper"
                type="password"
                v-model="password"
                placeholder="请输入密码">
      </mt-field>
    </div>
    <mt-button type="primary"
               class="ds-button-large mt-10 mb-10"
               v-on:click="signin">登录</mt-button>
    <div class="retrieve-wrapper">
      <div class="retrieve-item"
           @click="onRetrieve">
        <label id="retrieve-title">忘记密码？</label>
      </div>
    </div>
    <div class="bottom-wrapper"
         v-if="isShowWechat || isShowSina || isShowQQ">
      <div class="auth-title-wrapper">
        <div class="auth-line"></div>
        <label class="auth-title">第三方登录</label>
        <div class="auth-line"></div>
      </div>
      <div class="auth-bottom-wrapper">
        <div class="auth-item"
             v-if="isShowWechat"
             @click="onWechat">
          <i class="auth-item-icon iconfont wechat">&#xe647;</i>
          <label class="auth-title auth-item-title">微信</label>
        </div>
        <div class="auth-item"
             v-if="isShowSina"
             @click="onSina">
          <i class="auth-item-icon iconfont sina">&#xe646;</i>
          <label class="auth-title auth-item-title">微博</label>
        </div>
        <div class="auth-item"
             v-if="isShowQQ"
             @click="onTencent">
          <i class="auth-item-icon iconfont tencent">&#xe882;</i>
          <label class="auth-title auth-item-title">QQ</label>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
import { login, wechatLogin } from '../../../api/memberLogin'
import { isWechat } from '../../../util/wechat'
import { Indicator, Toast } from 'mint-ui'
import { mapMutations, mapActions, mapState } from 'vuex'
export default {
  name: 'Login',
  data () {
    return {
      username: '',
      password: ''
    }
  },
  beforeRouteEnter (to, from, next) {
    next(vm => {
      // 通过 `vm` 访问组件实例
      vm.utils.setCookie('referrer', encodeURIComponent(from.fullPath), 1 / 24)
    })
  },
  computed: {
    ...mapState({
      inviter_id: state => state.member.inviterId,
      config: state => state.config.config,
    }),
    isShowWechat: function () {
      if (isWechat() && this.config && this.config.weixin_isuse == 1) {
        return true
      }
      return false
    },
    isShowSina: function () {
      if (!isWechat() && this.config && this.config.sina_isuse == 1) {
        return true
      }
      return false
    },
    isShowQQ: function () {
      if (!isWechat() && this.config && this.config.qq_isuse == 1) {
        return true
      }
      return false
    },
  },
  created: function () {
    Indicator.close()
    let user_info = this.utils.getCookie('user_info')
    let token = this.utils.getCookie('key')
    if (user_info) {
      user_info = JSON.parse(decodeURIComponent(user_info))
      this.saveAuthInfo({ token: token, info: user_info })
      this.utils.clearCookie('user_info')
      this.utils.clearCookie('key')
      this.goHome()
    }
    if (this.$route.query.clear) {
      this.memberLogout()
    }
    this.fetchConfig({}).then(
      response => {
      },
      error => {
        Toast(error.message)
      }
    )
  },
  mounted () {
    let isTokenInvalid = this.$route.params.isTokenInvalid
    if (isTokenInvalid) {
      Toast('登录过期')
    }
  },
  methods: {
    ...mapMutations({
      saveAuthInfo: 'memberLogin',
      saveSellerAuthInfo: 'sellerLogin',
      memberLogout: 'memberLogout'
    }),
    ...mapActions({
      fetchConfig: 'fetchConfig'
    }),
    onTencent () {
      window.location.href = process.env.VUE_APP_API_HOST + '/api/oa_qq'
    },
    onSina () {
      window.location.href = process.env.VUE_APP_API_HOST + '/api/oa_sina'
    },
    signin () {
      let username = this.username
      let password = this.password
      if (username.length === 0) {
        Toast('请输入用户名/邮箱/手机号')
        return
      }
      // TODO: 用户名（为手机号或邮箱）校验
      if (password.length === 0) {
        Toast('请输入密码')
        return
      }
      if (password.length < 6) {
        Toast('至少输入6位密码')
        return
      }
      Indicator.open()
      login(username, password).then(
        response => {
          Indicator.close()
          this.saveAuthInfo({ token: response.result.token, info: response.result.info })
          if (response.result.seller_info) {
            this.saveSellerAuthInfo({ token: response.result.seller_token, info: response.result.seller_info })
          }
          this.goHome()
        },
        error => {
          Indicator.close()
          Toast(error.message)
        }
      )
    },

    goBack () {
      let isFromInfoEdit = this.$route.params.isFromInfoEdit
      let isFromShare = this.$route.params.isFromShare
      if (isFromInfoEdit || isFromShare) {
        this.$router.push('home')
      } else {
        this.$router.go(-1)
      }
    },

    goHome () {
      let referrer = this.utils.getCookie('referrer')
      if (referrer) { // 返回之前的页面
        referrer = decodeURIComponent(referrer)
        this.utils.clearCookie('referrer')
        this.$router.push({ path: referrer })
      } else {
        this.$router.push({ name: 'MemberIndex' })
      }
    },

    onSignup () {
      this.$router.push({ name: 'HomeMemberRegister' })
    },

    onRetrieve () {
      this.$router.push({ name: 'HomeMemberForget' })
    },

    onWechat () {
      let ref = encodeURIComponent(window.location.href)
      wechatLogin(ref, this.inviter_id).then(res => {
        window.location.href = res.result
      }).catch(function (error) {
        Toast(error.message)
      })
    }

  }
}
</script>

<style scoped lang='scss'>
.container {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: stretch;
  background-color: #f0f2f5;
  .top-wrapper {
    .input-wrapper {
      display: flex;
      align-content: center;
      align-items: center;
      padding-left: 0.5rem;
      background-color: #fff;
      height: 2.2rem;
      img {
        width: 1.2rem;
        height: 1.2rem;
        margin: 0;
        padding: 0;
      }
      input {
        flex: 1;
        font-size: 0.8rem;
      }
      .bottom-input {
        border-bottom-width: 0;
      }
    }
  }
}
.retrieve-wrapper {
  height: 2rem;
  margin-top: 0.5rem;
  display: flex;
  flex-direction: row;
  justify-content: flex-end;
}
.retrieve-item {
  width: 4rem;
  height: 2rem;
  margin-right: 0.5rem;
  display: flex;
  justify-content: center;
  align-items: center;
}
#retrieve-title {
  color: $mainColor;
  font-size: 0.75rem;
  text-align: center;
}
.bottom-wrapper {
  display: flex;
  flex: 1;
  flex-direction: column;
  justify-content: flex-end;
  align-items: stretch;
}
.auth-wrapper {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
  height: 13rem;
}
.auth-title-wrapper {
  height: 1rem;
  display: flex;
  flex-direction: row;
  justify-content: flex-start;
  align-items: center;
  margin-bottom: 2rem;
}
.auth-title {
  color: #8f8e94;
  font-size: 0.7rem;
}
.auth-line {
  display: flex;
  flex: 1;
  height: 1px;
  background-color: #d8d8d8;
  margin-left: 0.8rem;
  margin-right: 0.8rem;
}
.auth-item {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: center;
}
.auth-item-icon {
  width: 3rem;
  height: 3rem;
  text-align: center;
  font-size: 3rem;
}
.wechat {
  color: #2db304;
}
.sina {
  color: #f01414;
}
.tencent {
  color: #26a2ff;
}
.auth-item-title {
  margin-top: 0.6rem;
}
.auth-bottom-wrapper {
  display: flex;
  flex-direction: row;
  justify-content: space-around;
  align-items: center;
  margin-left: 0.75rem;
  margin-right: 0.75rem;
  margin-bottom: 1rem;
}
</style>
