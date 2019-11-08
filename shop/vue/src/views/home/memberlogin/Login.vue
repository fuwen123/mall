<template>
  <div class="login">
    <!-- 顶部关闭按钮 -->
    <div class="header back">
      <van-icon name="cross"
                @click="goBack"></van-icon>
    </div>

    <!-- logo -->
    <div class="logo">
      <img src="@/assets/image/logo.png"
           class="login-logo"
           alt="">

      <h4 class="title">欢迎登录蜂鸟智慧</h4>
    </div>

    <!-- 登录表单 -->
    <div class="login-form">

      <div class="input-item flex justify-start align-center">
        <div class="lable">+86 ></div>
        <div class="main-input">
          <input type="text"
                 placeholder="请输入手机号码"
                 v-model="mobile"
                 @focus="onFocus()"
                 @blur.prevent="onBlur()">
        </div>
      </div>

      <div class="input-item flex justify-start align-center">
        <div class="main-input">
          <input :type="eye ? 'text':'password'"
                 placeholder="请输入登录密码"
                 v-model="password"
                 @focus="onFocus()"
                 @blur.prevent="onBlur()">
        </div>
        <div class="right-icon">
          <van-icon :name="eye ? 'eye-o':'closed-eye'"
                    @click="eye = !eye"></van-icon>
        </div>
      </div>
    </div>

    <!-- 忘记密码等 -->
    <div class="handle">
      <!-- 登录按钮 -->
      <van-button round
                  size="large"
                  :class="password&&mobile?'btn-login':'btn-login active'"
                  @click="submitAjax">登录</van-button>

      <!-- 忘记密码操作 -->
      <div class="flex justify-between align-center">
        <router-link :to="{name: 'HomeMemberRegister'}"
                     class="signUp">新用户注册</router-link>
        <router-link :to="{name: 'HomeMemberForget'}"
                     class="forget_pass">忘记密码?</router-link>
      </div>
    </div>

    <div class="footer"
         v-show="showFooter">
      <img src="@/assets/image/login-footer.png" />
    </div>
    <!-- 第三方登录 -->
    <!-- <div class="TheThird-login flex justify-center">
      <div class="login-icon"
           v-if="isweixin">
        <img src="img/wexin.png"
             @click="wxlogin" />
        <p>微信登录</p>
      </div>
    </div> -->
  </div>
</template>

<script>
// import { login, wechatLogin } from '../../../api/memberLogin'
// import { Indicator, Toast } from 'mint-ui'
// import { mapMutations, mapActions, mapState } from 'vuex'

// import { Dialog, Toast } from 'vant'

import { mapState, mapMutations } from 'vuex'
import { isWechat } from '@/util/wechat'
import validator from '@/util/validator'
import { login, wechatLogin } from '@/api/memberLogin'

var hasHistory = false // 记录是否有上一页

export default {
  name: 'Login',
  data () {
    return {
      eye: false, // 密码是否显示
      mobile: null, // 手机账号
      password: null, // 密码
      showState: true,
      inputType: 'password',
      isweixin: isWechat(),
      showFooter: true
    }
  },

  /**
   * 路由守卫
   */
  beforeRouteEnter (to, form, next) {
    next()
    if (form.name) {
      hasHistory = true
    }
  },

  methods: {
    ...mapMutations({
      saveAuthInfo: 'memberLogin',
      saveSellerAuthInfo: 'sellerLogin',
      memberLogout: 'memberLogout'
    }),
    /**
     * 回到首页
     */
    goBack () {
      this.$router.push({ name: 'HomeIndex' })
    },

    /**
     * 登陆
     */
    submitAjax () {
      if (!validator('mobile', this.mobile)) {
        return this.$toast({ message: '请输入正确手机号码' })
      }
      if (!this.password) {
        return this.$toast({ message: '请输入密码' })
      }

      login(this.mobile, this.password).then(res => {
        // 用户登陆信息
        this.saveAuthInfo({ token: res.result.token, info: res.result.info })
        // 商家信息
        res.result.seller_info && this.saveSellerAuthInfo({ token: res.result.seller_token, info: res.result.seller_info })

        if (hasHistory) {
          this.$router.go(-1)
        } else {
          this.goBack()
        }
      }).catch(res => {
        // this.$router.go(-1)
        this.$toast({ message: res.message })
      })
    },

    /**
     * 键盘弹起兼容
     */
    onFocus () {
      this.showFooter = false
    },
    onBlur () {
      this.showFooter = true
    }
  }
}
</script>

<style scoped lang='scss'>
.header {
  margin: 10px;
  font-size: 25px;
}

.logo {
  width: 100%;
  text-align: center;
  margin: 45px auto;
  color: #333333;
  font-size: 24px;
  .login-logo {
    width: 100px;
    height: 100px;
    margin: 0 auto 17px auto;
  }
}

.login-form {
  width: 77%;
  margin: 0 auto;
  font-size: 13px;
  .input-item {
    width: 100%;
    margin-bottom: 35px;
    border-bottom: 1px solid #cccccc;
    padding-bottom: 10px;
    .main-input {
      flex: 1;
      font-size: 18px;
      input {
        width: 100%;
        outline: none;
        border: none;
        margin: 0 10px;
        &::placeholder {
          color: #ccc;
        }
        &::-webkit-autofill {
          background-color: #fff !important; //设置成元素原本的颜色
          background-image: none;
          color: rgb(0, 0, 0);
        }
      }
    }

    .right-icon {
      font-size: 20px;
    }
  }
}

.handle {
  width: 77%;
  margin: 0 auto 50px auto;
  font-size: 13px;
  .btn-login {
    background-color: $mainColor;
    color: #ffffff;
    margin-bottom: 15px;
    &.active {
      background-color: $diasbleColor;
    }
  }

  .flex {
    width: 100%;
    color: #333333;
  }
  .signUp {
    color: #000;
  }
  .forget_pass {
    color: #cecece;
  }
}
.TheThird-login {
  margin-top: 20px;
  .login-icon {
    width: 50px;
    height: 50px;
    img {
      display: block;
      width: 100%;
      height: 100%;
    }
    p {
      font-size: 12px;
      color: #999;
      margin-top: 3px;
    }
  }
}

// 底部装饰
.footer {
  position: fixed;
  bottom: 0;
  left: 0;
  width: 100%;
  z-index: 0;
  img {
    width: 100%;
  }
}
</style>
