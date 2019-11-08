<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header class="common-header" title="商家登录">
                <mt-button icon="back" slot="left" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div class="top-wrapper">
            <div>
                <mt-field class="input-wrapper" v-model="sellername" placeholder="商家账户" type="text" />
            </div>
            <div>
                <mt-field type="password" class="input-wrapper" v-model="password" placeholder="请输入密码" maxlength="20" />
            </div>
        </div>
        <mt-button type="primary" class="ds-button-large mt-10 mb-10" v-on:click="signin">登录</mt-button>
    </div>
</template>

<script>
import { sellerlogin } from '../../../api/sellerLogin'
import { Indicator, Toast } from 'mint-ui'
import { mapMutations } from 'vuex'
export default {
  name: 'login',
  data () {
    return {
      sellername: '',
      password: ''
    }
  },
  created: function () {
    Indicator.close()
    if (this.$route.query.clear) {
      this.sellerLogout()
    }
  },
  methods: {
    ...mapMutations({
      saveAuthInfo: 'sellerLogin',
      saveMemberAuthInfo: 'memberLogin',
      sellerLogout: 'sellerLogout'
    }),

    signin () {
      let sellername = this.sellername
      let password = this.password
      if (sellername.length === 0) {
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
      sellerlogin(sellername, password).then(
        response => {
          Indicator.close()
          this.saveAuthInfo({ token: response.result.token, info: response.result.info })
          this.saveMemberAuthInfo({ token: response.result.member_token, info: response.result.member_info })
          this.goSellerIndex()
        },
        error => {
          Indicator.close()
          Toast(error.message)
        }
      )
    },
    goSellerIndex () {
      this.$router.push({ name: 'SellerIndex' })
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
        background-color: #F0F2F5;
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
        font-size:.8rem;
    }
    .bottom-input {
        border-bottom-width: 0;
    }
    }
    }
    }
</style>
