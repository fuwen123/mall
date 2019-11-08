<template>
	<div class="container">
		<div class="section-wrapper top-wrapper">
			<div >
				<mt-field class="input-wrapper" v-model="username" placeholder="用户名" maxlength="11" />
				<mt-field
						type="password"
						class="input-wrapper"
						v-model="password"
						placeholder="密码"
						maxlength="20"
				/>
			</div>
		</div>

		<mt-button class="ds-button-large mt-10 mb-10" type="primary" v-on:click="onSubmit">绑定</mt-button>

	</div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import { mapState, mapMutations } from 'vuex'
import { bind } from '../../../api/memberLogin'
export default {
  name: 'BindOld',
  data () {
    return {
      username: '',
      password: '',
      wxinfo: false
    }
  },

  created: function () {
    let wxinfo = this.utils.getCookie('wxinfo')

    if (!wxinfo) {
      Toast('绑定信息不存在')
      this.$router.push({ name: 'HomeMemberLogin' })
    } else {
      wxinfo = JSON.parse(decodeURIComponent(wxinfo))
      this.wxinfo = wxinfo
    }
  },
  computed: {
    ...mapState({
      inviter_id: state => state.member.inviterId
    })
  },

  methods: {
    ...mapMutations({
      saveAuthInfo: 'memberLogin'
    }),

    check () {
      let username = this.username
      let password = this.password
      if (username.length === 0) {
        Toast('请输入用户名')
        return false
      }

      if (password.length === 0) {
        Toast('请输入密码')
        return false
      }

      return true
    },
    signup () {
      if (!this.check()) {
        return
      }
      Indicator.open()

      bind(0, 'wx', this.wxinfo.openid, this.wxinfo.unionid, this.wxinfo.nickname, this.wxinfo.headimgurl, this.username, '', this.password, '', this.inviter_id).then(
        response => {
          Indicator.close()
          this.utils.clearCookie('wxinfo')
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

	}
	.top-wrapper {margin-top:0.5rem;}
	.bottom-wrapper {margin-top:0.5rem;}
	.tips {color: #c3c3c3;font-size:0.6rem;margin-left:0.9rem;margin-top:0.5rem;text-align: left;}
	.countdown {min-width:4rem;margin-right:0.5rem;}
</style>
