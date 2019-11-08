<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header class="common-header"
                 title="密码找回">
        <mt-button icon="back"
                   slot="left"
                   @click="$router.go(-1)"></mt-button>
      </mt-header>
    </div>
    <div class="container">
      <div class="section-wrapper top-wrapper">
        <div>
          <mt-field class="input-wrapper"
                    v-model="username"
                    placeholder="请输入手机号"
                    type="number"
                    :attr="{ oninput: 'if(value.length>11)value=value.slice(0,11)' }" />
        </div>
        <mt-field class="input-wrapper"
                  placeholder="验证码"
                  v-model="verifyCodeMobile"
                  type="number"
                  :attr="{ oninput: 'if(value.length>6)value=value.slice(0,6)' }">
          <mt-button @click="sendVerifyCodeMobile"
                     class="btn"
                     type="default"
                     size="small"
                     plain>{{sendStateTextMobile}}</mt-button>
        </mt-field>
      </div>
      <div class="section-wrapper bottom-wrapper">
        <div>
          <mt-field class="input-wrapper"
                    type="password"
                    v-model="password"
                    placeholder="设置密码"
                    maxlength="20" />
        </div>
        <div>
          <mt-field type="password"
                    class="input-wrapper"
                    v-model="confirmPassword"
                    placeholder="确认密码"
                    maxlength="20" />
        </div>
      </div>
      <label class="tips">6-20位数字/字母/符号</label>
      <mt-button type="primary"
                 class="ds-button-large"
                 v-on:click="onSubmit">确定</mt-button>
    </div>

  </div>
</template>

<script>
import { forget } from '../../../api/memberForget'
import { getSmsCaptcha } from '../../../api/common'
import { mapMutations, mapActions, mapState } from 'vuex'
import { Toast } from 'mint-ui'
export default {
  name: 'HomeMemberForget',
  components: {
  },
  data () {
    return {
      username: '',
      password: '',
      confirmPassword: '',
      verifyCodeMobile: '',
      canSendMobile: true,
      timeIntervalMobile: false,
      sendStateTextMobile: '发送'
    }
  },
  beforeDestroy: function () {
    clearInterval(this.timeIntervalMobile)
  },
  methods: {
    ...mapMutations({
      saveAuthInfo: 'memberLogin'
    }),
    onSubmit () {
      forget(this.username, this.verifyCodeMobile, this.password, this.confirmPassword).then(
        response => {
          this.saveAuthInfo({ token: response.result.token, info: response.result.info })
          this.$router.push({ name: 'MemberIndex' })
        },
        error => {
          Toast(error.message)
        }
      )
    },
    sendVerifyCodeMobile () {
      if (!this.canSendMobile) {
        return
      }
      getSmsCaptcha(3, this.username).then(res => {
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
  background-color: #f0f2f5;
  .section-wrapper {
    display: flex;
    flex-direction: column;
    justify-content: flex-start;
    align-items: stretch;
    background-color: #ffffff;
    border-top: 1px solid #e8eaed;
    border-bottom: 1px solid #e8eaed;
    .input-wrapper {
      display: flex;
      flex-direction: row;
      align-content: flex-start;
      align-items: center;
      background-color: #fff;
      height: 2.2rem;
      padding-left: 0.5rem;
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
.top-wrapper {
  margin-top: 0.5rem;
}
.bottom-wrapper {
  margin-top: 0.5rem;
}
.tips {
  color: #c3c3c3;
  font-size: 0.6rem;
  margin-left: 0.9rem;
  margin-top: 0.5rem;
  text-align: left;
}
.ds-button-large {
  margin-top: 1rem;
}
.countdown {
  width: 5.6rem;
  height: 1.5rem;
  margin-left: 0.5rem;
  margin-right: 0.5rem;
}
</style>
