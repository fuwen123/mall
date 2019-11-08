<template>
	<div class="container">
		<div class="section-wrapper top-wrapper">
			<div >
				<mt-field class="input-wrapper" v-model="username" placeholder="请输入手机号" type="number" :attr="{ oninput: 'if(value.length>11)value=value.slice(0,11)' }" />
			</div>
			<div >
				<mt-field class="input-wrapper" v-model="pictureCode" placeholder="请输入图片验证码" maxlength="6">
				<img @click="changePictureCode" :src="pictureCodeUrl" class="countdown" >
				</mt-field>
			</div>
			<div >
				<mt-field class="input-wrapper" v-model="code" placeholder="请输入短信验证码" type="number" :attr="{ oninput: 'if(value.length>6)value=value.slice(0,6)' }" >
				<mt-button @click="sendVerifyCodeMobile" class="countdown" type="default" size="small" plain>{{sendStateTextMobile}}</mt-button>
				</mt-field>
			</div>
		</div>
		<div class="section-wrapper bottom-wrapper">
			<div >
				<mt-field
					type="password"
					class="input-wrapper"
					v-model="password"
					placeholder="设置密码"
					maxlength="20"
				/>
			</div>
			<div >
				<mt-field
					type="password"
					class="input-wrapper"
					v-model="confirmPassword"
					placeholder="确认密码"
					maxlength="20"
				/>
			</div>
		</div>
		<label class="tips">6-20位数字/字母/符号</label>

		<mt-button class="ds-button-large mt-10 mb-10" type="primary" v-on:click="onSubmit">注册</mt-button>
		<div class="link-wrapper">
			<span class="left-text">点击注册表示同意</span>
			<span class="right-text" @click="onAgreement">《用户协议》</span>
		</div>
	</div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import { mapState, mapMutations } from 'vuex'
import { registerByMobile } from '../../../api/memberRegister'
import { getSmsCaptcha, checkPictureCaptcha } from '../../../api/common'
export default {
  name: 'RegisterByMobile',
  data () {
    return {
      pictureCodeUrl: '',
      pictureCode: '',
      pictureCodeValid: false,
      pictureCodeWait: false,
      username: '',
      code: '',
      password: '',
      confirmPassword: '',
      aggrementUrl: '',
      verifyCodeMobile: '',
      canSendMobile: true,
      timeIntervalMobile: false,
      sendStateTextMobile: '发送'
    }
  },
  mounted () {
    this.changePictureCode()
  },
  created: function () {

  },
  computed: {
    ...mapState({
      inviter_id: state => state.member.inviterId
    })
  },
  watch: {
    pictureCode: function (val) {
      if (val.length >= 4) {
        this.pictureCodeWait = true
        checkPictureCaptcha(val).then(
          response => {
            this.pictureCodeWait = false
            this.pictureCodeValid = true
          },
          error => {
            this.pictureCodeWait = false
            Toast(error.message)
          }
        )
      }
    }
  },
  beforeDestroy: function () {
    clearInterval(this.timeIntervalMobile)
  },
  methods: {
    ...mapMutations({
      saveAuthInfo: 'memberLogin'
    }),
    goBack () {
      this.$router.go(-1)
    },
    goProfile () {
      this.$router.go(-2)
    },
    goProfileAdd () {
      this.$router.push({ name: 'profileAdd' })
    },
    changePictureCode () {
      this.pictureCodeUrl = process.env.VUE_APP_API_HOST + '/Seccode/makecode?r=' + Math.random()
    },
    sendVerifyCodeMobile () {
      if (!this.pictureCode) {
        Toast('请先输入图片验证码')
        return
      }
      if (this.pictureCodeWait) {
        Toast('正在验证图片验证码，请稍等')
        return
      }
      if (!this.pictureCodeValid) {
        Toast('图片验证码错误')
        return
      }
      if (!this.username) {
        Toast('请先输入手机号')
        return
      }
      if (!this.canSendMobile) {
        return
      }
      getSmsCaptcha(1, this.username).then(res => {
        this.canSendMobile = false
        let second = 60
        Toast(res.message)
        let _this = this
        this.timeIntervalMobile = setInterval(function () {
          if (second <= 0) {
            _this.canSendMobile = true
            _this.sendStateTextMobile = '发送'
            clearInterval(_this.timeIntervalMobile)
          } else {
            _this.sendStateTextMobile = second + 's'
          }
          second--
        }, 1000)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    check () {
      let username = this.username
      let code = this.code
      let password = this.password
      let confirmPassword = this.confirmPassword
      if (username.length === 0) {
        Toast('请输入手机号')
        return false
      }
      if (code.length === 0) {
        Toast('请输入验证码')
        return false
      }
      if (code.length !== 6) {
        Toast('请输入6位验证码')
        return false
      }
      if (password.length === 0) {
        Toast('请输入密码')
        return false
      }
      if (password.length < 6 || password.length > 20) {
        Toast('请输入6-20个字符的密码')
        return false
      }
      if (confirmPassword.length === 0) {
        Toast('请输入确认密码')
        return false
      }
      if (password.length !== confirmPassword.length) {
        Toast('确认密码与输入密码不一致')
        return false
      }
      if (password !== confirmPassword) {
        Toast('确认密码与输入密码不一致')
        return false
      }
      return true
    },
    signup () {
      if (!this.check()) {
        return
      }
      Indicator.open()
      let inviterId = this.inviter_id // 获取邀请人id

      registerByMobile(this.username, this.password, this.confirmPassword, this.code, inviterId).then(
        response => {
          Indicator.close()
          this.saveAuthInfo({ token: response.result.token, info: response.result.info })
          this.$router.push({ name: 'MemberIndex' })
        },
        error => {
          Indicator.close()
          Toast(error.message)
        }
      )
    },
    onSubmit () {
      this.signup()
    },
    onAgreement () {
      this.$router.push({
        name: 'HomeDocument',
        query: { type: '' }
      })
    }
  }
}
</script>

<style scoped lang="scss">
.container {
display: flex;
flex-direction: column;
justify-content: flex-start;
	align-items: stretch;
	//background-color: #e93b3d;
	.section-wrapper {
		display: flex;
		flex-direction: column;
		justify-content: flex-start;
		align-items: stretch;
		background-color: #ffffff;
		border-top: 1px solid #e8eaed;
		border-bottom: 1px solid #e8eaed;
		.input-wrapper {
			font-size: .8rem;
			display: flex;
			flex-direction: row;
			align-content: flex-start;
			align-items: center;
			background-color: #fff;
			height:2.2rem;
			padding-left:0.5rem;
			input {
				flex: 1;
			}
			.bottom-input {
				border-bottom-width: 0;
			}
			img{height:1.75rem}
		}
	}
	.link-wrapper {
		height:2.2rem;
		margin-top:0.5rem;
		display: flex;
		flex-direction: row;
		justify-content: center;
		align-items: stretch;
		span {
			font-size:0.7rem;
			display: flex;
			flex-direction: row;
			justify-content: center;
			align-items: center;
		}
		.left-text {
			color: #c3c3c3;
		}
		.right-text {
			color: $primaryColor;
		}
	}
}
.top-wrapper {margin-top:0.5rem;}
.bottom-wrapper {margin-top:0.5rem;}
.tips {color: #c3c3c3;font-size:0.6rem;margin-left:0.9rem;margin-top:0.5rem;text-align: left;}
.countdown {min-width:4rem;margin-right:0.5rem;}
</style>
