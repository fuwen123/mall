<template>
  <div class="container">
    <div class="form-item">
      <div class="lable">+86></div>
      <van-field v-model="formData.mobile"
                 clearable
                 placeholder="请输入手机号" />
    </div>
    <div class="form-item">
      <van-field v-model="formData.mobile"
                 clearable
                 placeholder="请输入图文验证码"
                 class="border-bottom" />
    </div>
    <div class="form-item">
      <van-field v-model="formData.smsCode"
                 clearable
                 placeholder="请输入短信验证码"
                 class="border-bottom">
        <van-button slot="button"
                    round
                    size="normal"
                    color="#000000"
                    type="primary">获取短信验证码</van-button>
      </van-field>
    </div>
    <div class="form-item">
      <van-field v-model="formData.password"
                 clearable
                 type="password"
                 placeholder="请设置登录密码"
                 class="border-bottom" />
    </div>
    <div class="form-item">
      <van-field v-model="formData.password1"
                 clearable
                 type="password"
                 placeholder="请再次确认登录密码"
                 class="border-bottom" />
    </div>
    <div class="form-item">
      <van-field v-model="formData.invitationCode"
                 clearable
                 placeholder="请填写推荐人手机"
                 class="border-bottom" />
    </div>

    <!-- 同意协议 -->
    <van-checkbox v-model="agree"
                  class="agree-document checkbox">
      同意
      <router-link class="text-link"
                   :to="{name:'HomeDocument'}">《会员注册协议》</router-link>
    </van-checkbox>

    <!-- 提交表单 -->
    <van-button slot="button"
                round
                :disabled="btnDisable"
                size="normal"
                type="primary"
                class="submit-btn"
                @click="submitData">确定</van-button>

  </div>
</template>

<script>
import { mapMutations } from 'vuex'
import validator from '@/util/validator'
import { register } from '@/api/memberRegister'
const errMsg = {
  mobile: '请输入正确的手机号',
  password: '请设置登录密码',
  password1: '请再次确认登录密码',
  smsCode: '请输入短信验证码'
}
export default {
  name: 'Register',
  computed: {
    btnDisable: function () {
      return !this.checkData().state
    }
  },
  data () {
    return {
      // 表单数据
      formData: {
        mobile: null, // 手机号
        smsCode: null, // 短信验证码
        password: null, // 密码
        password1: null, // 确认密码
        invitationCode: null // 邀请人的手机号码
      },
      agree: false // 同意会员注册协议
    }
  },
  created: function () {
    if (this.$route.query.inviter_id) {
      this.memberInviterId({ inviterId: this.$route.query.inviter_id })
    }
  },
  mounted () {
  },
  methods: {
    ...mapMutations({
      memberInviterId: 'memberInviterId'
    }),
    goBack () {
      this.$router.go(-1)
    },

    /**
     * 提交数据
     */
    submitData: function () {
      var checkRes = this.checkData()
      if (!checkRes.state) {
        return this.$toast(checkRes.errMsg)
      } else {
        // 注册接口
        register().then(res => { }).catch(res => { })
      }
    },

    /**
     * 验证数据
     */
    checkData () {
      var res = { state: true, errMsg: '' }

      // 没同意协议的，直接return false
      if (!this.agree) {
        return {
          state: false,
          errMsg: '请同意会员注册协议'
        }
      }

      // 验证每一项是否已经填写
      this._.each(this.formData, (item, index) => {
        if (res.state && errMsg[index]) {
          // 验空
          if (!item) {
            res.state = false
            res.errMsg = errMsg[index]
          } else if (index === 'mobile') {
            res.state = validator('mobile', item) // 验证手机号
            res.errMsg = res.state ? '' : errMsg[index]
          } else if (index === 'password1') {
            res.state = this.formData.password === this.formData.password1
            res.errMsg = res.state ? '' : '两次输入密码不一致，请检查后重试'
          }
        }
      })
      return res
    }
  }
}
</script>

<style scoped lang="scss">
.container {
  padding: 10px 40px;

  .form-item {
    display: flex;
    justify-content: flex-start;
    font-size: 18px;
    align-items: center;
    border-bottom: 1px solid #cccccc;
    height: 70px;
    .lable {
      font-size: 13px;
    }
  }

  .agree-document {
    font-size: 13px;
    margin: 15px auto;
    .text-link {
      color: #2b48f6;
    }
  }

  .submit-btn {
    background-color: $mainColor;
    border-color: $mainColor;
    width: 100%;
    font-size: 16px;
    font-weight: bold;
  }
}
</style>
