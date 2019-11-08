<template>
  <div class="common-send-code">
    <mt-cell title="验证方式">
      <mt-radio v-model="verifyType"
                :options="verifyTypeOptions">
      </mt-radio>
    </mt-cell>

    <mt-field label="验证码"
              v-model="verifyCode">
      <mt-button @click="sendAuthCode"
                 class="btn"
                 type="default"
                 size="small"
                 plain>{{sendStateText}}</mt-button>
    </mt-field>
    <mt-button class="ds-button-large"
               type="primary"
               @click='checkAuthCode'>下一步</mt-button>
  </div>
</template>

<script>
import { mapState } from 'vuex'
import { sendAuthCode, checkAuthCode } from '../../../api/memberSetting'
import { Toast } from 'mint-ui'
export default {
  name: 'CommonSendCode',
  components: {
  },
  data () {
    return {
      verifyType: 'email',
      verifyCode: '',
      canSend: true,
      sendStateText: '发送',
      verifyTypeOptions: []
    }
  },
  beforeDestroy: function () {
    clearInterval(this.time_interval)
  },
  created: function () {
    if (this.user.member_mobilebind && this.user.member_mobile) {
      this.verifyTypeOptions.push({
        label: '手机',
        value: 'mobile'
      })
      this.verifyType = 'mobile'
    }
    if (this.user.member_emailbind && this.user.member_email) {
      this.verifyTypeOptions.push({
        label: '邮箱',
        value: 'email'
      })
    }
  },
  computed: {
    ...mapState({
      user: state => state.member.info
    })
  },
  methods: {
    sendAuthCode () {
      if (!this.canSend) {
        return
      }
      sendAuthCode(this.verifyType).then(res => {
        this.canSend = false
        let second = 60
        Toast(res.message)
        let _this = this
        this.time_interval = setInterval(function () {
          if (second <= 0) {
            _this.canSend = true
            _this.sendStateText = '发送'
            clearInterval(_this.time_interval)
          } else {
            _this.sendStateText = second + 's'
          }

          second--
        }, 1000)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    checkAuthCode () {
      checkAuthCode(this.verifyCode).then(res => {
        this.$emit('checkSuccess')
      }).catch(function (error) {
        Toast(error.message)
      })
    }

  }
}
</script>

<style  lang="scss" scoped>
.common-send-code {
  position: relative;
  z-index: 100;
  .btn {
    width: 4rem;
  }
  .mint-radiolist {
    display: flex;
    .mint-cell {
      flex: 1;
      .mint-radio-input:checked + .mint-radio-core {
        background-color: #e93b3d !important;
        border-color: #e93b3d !important;
      }
    }
  }
}
.ds-button-large {
  margin-top: 1rem;
}
</style>
