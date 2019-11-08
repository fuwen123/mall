<template>
    <div class="member-account-set">
        <div class="common-header-wrap">
            <mt-header title="账号设置" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div class="menu-list">
            <div v-show="(user.member_mobilebind && user.member_mobile) || (user.member_emailbind && user.member_email)" @click='popupVisible("password")'><mt-cell class="menu-item" title="修改登录密码" ><span class="iconfont right-arrow">&#xe600;</span></mt-cell></div>
            <div v-show="(user.member_mobilebind && user.member_mobile) || (user.member_emailbind && user.member_email)" @click='popupVisible("paypwd")'><mt-cell class="menu-item" title="修改支付密码" ><span class="iconfont right-arrow">&#xe600;</span></mt-cell></div>
            <div @click='popupVisible("mobile")'><mt-cell class="menu-item" :title="(user.member_mobilebind?'修改':'绑定')+'手机'">{{user.member_mobile}}<span class="iconfont right-arrow">&#xe600;</span></mt-cell></div>
            <div @click='popupVisible("email")'><mt-cell class="menu-item" :title="(user.member_emailbind?'修改':'绑定')+'邮箱'">{{user.member_email}}<span class="iconfont right-arrow">&#xe600;</span></mt-cell></div>
        </div>
        <mt-button class="ds-button-large" type="primary" @click="logout">退出登录</mt-button>

        <!--修改手机-->
        <mt-popup v-model="editMobileVisible" position="right" class="common-popup-wrapper">
            <div class="common-header-wrap">
                <mt-header :title="(user.member_mobilebind?'修改':'绑定')+'手机'" class="common-header">
                    <mt-button slot="left" icon="back" @click="editMobileVisible=false"></mt-button>
                </mt-header>
            </div>
            <div class="common-popup-content">
                <mt-field class="menu-item" label="手机" v-model="mobile"  type="number" :attr="{ oninput: 'if(value.length>11)value=value.slice(0,11)' }" />
                <mt-field label="验证码" v-model="verifyCodeMobile" type="number" :attr="{ oninput: 'if(value.length>6)value=value.slice(0,6)' }" >
                    <mt-button @click="sendVerifyCodeMobile" class="btn" type="default" size="small" plain>{{sendStateTextMobile}}</mt-button>
                </mt-field>
                <mt-button class="ds-button-large" type="primary" @click='updateMobile' >提交</mt-button>
            </div>
        </mt-popup>

        <!--修改邮箱-->
        <mt-popup v-model="editEmailVisible" position="right" class="common-popup-wrapper">
            <div class="common-header-wrap">
                <mt-header :title="(user.member_emailbind?'修改':'绑定')+'邮箱'" class="common-header">
                    <mt-button slot="left" icon="back" @click="editEmailVisible=false"></mt-button>
                </mt-header>
            </div>
            <div class="common-popup-content">
                <mt-field class="menu-item" label="邮箱" type="text" v-model="email" />
                <mt-button class="ds-button-large"  type="primary" @click='sendVerifyCodeEmail' >提交</mt-button>
            </div>
        </mt-popup>

        <!--修改登录密码-->
        <mt-popup v-model="editPasswordVisible" position="right" class="common-popup-wrapper">
            <div class="common-header-wrap">
                <mt-header title="修改登录密码" class="common-header">
                    <mt-button slot="left" icon="back" @click="editPasswordVisible=false"></mt-button>
                </mt-header>
            </div>
            <div class="common-popup-content">
                <mt-field class="menu-item" label="新密码" type="password" v-model="password1" />
                <mt-field class="menu-item" label="确认密码" type="password" v-model="password2" />
                <mt-button class="ds-button-large" type="primary" @click='updatePassword' >提交</mt-button>
            </div>
        </mt-popup>

        <!--修改支付密码-->
        <mt-popup v-model="editPaypwdVisible" position="right" class="common-popup-wrapper">
            <div class="common-header-wrap">
                <mt-header title="修改支付密码" class="common-header">
                    <mt-button slot="left" icon="back" @click="editPaypwdVisible=false"></mt-button>
                </mt-header>
            </div>
            <div class="common-popup-content">
                <mt-field class="menu-item" label="新密码" type="password" v-model="paypwd1" />
                <mt-field class="menu-item" label="确认密码" type="password" v-model="paypwd2" />
                <mt-button class="ds-button-large"  type="primary" @click='updatePaypwd'>提交</mt-button>
            </div>
        </mt-popup>

        <mt-popup v-model="sendAuthCode" position="right" class="common-popup-wrapper">
            <div class="common-header-wrap">
                <mt-header title="身份验证" class="common-header">
                    <mt-button slot="left" icon="back" @click="sendAuthCode=false"></mt-button>
                </mt-header>
            </div>
            <div class="common-popup-content">
                <common-send-code @checkSuccess="checkSuccess" />
            </div>
        </mt-popup>
    </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import { Toast } from 'mint-ui'
import { logout, getMemberInfo } from '../../../api/member'
import CommonSendCode from '../common/CommonSendCode'
import { updateUserMobile, updateUserPassword, updateUserPaypwd, bindUserMobile, bindUserEmail } from '../../../api/memberSetting'
export default {
  components: {
    CommonSendCode
  },
  name: 'MemberSetting',
  data () {
    return {
      sendAuthCode: false,
      authType: 'mobile',
      editPaypwdVisible: false,
      paypwd1: '',
      paypwd2: '',
      editMobileVisible: false,
      mobile: '',
      editEmailVisible: false,
      email: '',
      editPasswordVisible: false,
      password1: '',
      password2: '',
      verifyCodeMobile: '',
      canSendMobile: true,
      timeIntervalMobile: false,
      sendStateTextMobile: '发送',
      canSendEmail: true,
      timeIntervalEmail: false,
      sendStateTextEmail: '发送'

    }
  },
  beforeDestroy: function () {
    clearInterval(this.timeIntervalMobile)
    clearInterval(this.timeIntervalEmail)
  },
  created: function () {
    getMemberInfo().then(res => {
      this.memberUpdate({info:res.result.member_info})
      if (!res.result.member_info.member_emailbind) { // 如果没有绑定邮箱则输入项自动填写上次的邮箱
        this.email = res.result.member_info.member_email
      }
      if (!res.result.member_info.member_mobilebind) { // 如果没有绑定手机则输入项自动填写上次的手机
        this.mobile = res.result.member_info.member_mobile
      }
    }, error => {}
    )
  },
  computed: {
    ...mapState({
      user: state => state.member.info
    })
  },
  methods: {
    ...mapMutations({
      memberUpdate: 'memberUpdate',
      memberEdit: 'memberEdit',
      memberLogout: 'memberLogout'
    }),
    logout () {
      logout(this.user.member_name).then(res => {
        this.memberLogout()
        this.$router.push({ name: 'HomeMemberLogin' })
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    popupVisible (authType) {
      this.authType = authType
      switch (authType) {
        case 'mobile':
          if (this.user.member_mobilebind && this.user.member_mobile) { // 已绑定手机则需要验证
            this.sendAuthCode = true
          } else {
            this.editMobileVisible = true
          }
          break
        case 'email':
          if (this.user.member_emailbind && this.user.member_email) { // 已绑定邮箱则需要验证
            this.sendAuthCode = true
          } else {
            this.editEmailVisible = true
          }
          break
        case 'password':
          this.sendAuthCode = true
          break
        case 'paypwd':
          this.sendAuthCode = true
          break
      }
    },
    checkSuccess () {
      switch (this.authType) {
        case 'mobile':
          this.editMobileVisible = true
          break
        case 'email':
          this.editEmailVisible = true
          break
        case 'password':
          this.editPasswordVisible = true
          break
        case 'paypwd':
          this.editPaypwdVisible = true
          break
      }
    },
    // 更改绑定手机号
    updateMobile () {
      updateUserMobile(this.verifyCodeMobile).then(res => {
        this.memberEdit({ member_mobile: this.mobile, member_mobilebind: 1 })
        Toast(res.message)
        this.$router.go(0)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    // 更改登录密码
    updatePassword () {
      updateUserPassword(this.password1, this.password2).then(res => {
        Toast(res.message)
        this.$router.go(0)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    // 更改支付密码
    updatePaypwd () {
      updateUserPaypwd(this.paypwd1, this.paypwd2).then(res => {
        Toast(res.message)
        this.$router.go(0)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    // 发送手机验证码
    sendVerifyCodeMobile () {
      if (!this.canSendMobile) {
        return
      }
      bindUserMobile(this.mobile).then(res => {
        this.memberEdit({ member_mobile: this.mobile, member_mobilebind: 0 })
        this.canSendMobile = false
        let second = 60
        Toast(res.message)
        let _this = this
        this.timeIntervalMobile = setInterval(function () {
          if (second <= 0) {
            _this.canSendMobile = true
            _this.sendStateTextMobile = '发送'
            clearInterval(_this.timeIntervalMobile)
          }else{
              _this.sendStateTextMobile = second + 's'
          }

          second--
        }, 1000)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    // 发送邮箱验证码
    sendVerifyCodeEmail () {
      if (!this.canSendEmail) {
        return
      }
      bindUserEmail(this.email).then(res => {
        this.memberEdit({ member_email: this.email, member_emailbind: 0 })
        this.canSendEmail = false
        let second = 60
        Toast(res.message)
        let _this = this
        this.timeIntervalEmail = setInterval(function () {
          if (second <= 0) {
            _this.canSendEmail = true
            _this.sendStateTextEmail = '发送'
            clearInterval(_this.timeIntervalEmail)
          }else{
              _this.sendStateTextEmail = second + 's'
          }

          second--
        }, 1000)
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  }
}
</script>

<style  lang="scss" scoped>
  .member-account-set {position:relative;z-index:100;
      .menu-list{
          .menu-item {
              .right-arrow{transform: rotate(-90deg);color:#ddd;font-size: .6rem;}
          }
      }
  }
  .ds-button-large{margin-top: 1rem;}
</style>
