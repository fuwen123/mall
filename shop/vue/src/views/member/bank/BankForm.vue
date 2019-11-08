<template>
	<div class="container">
		<div class="common-header-wrap">
			<mt-header :title="getTitle" class="common-header">
				<mt-button slot="left" icon="back" @click="goBack"></mt-button>
			</mt-header>
		</div>
		<mt-cell title="账户类型">
			<mt-radio
					v-model="bank_info.memberbank_type"
					:options="memberbank_type_options">
			</mt-radio>
		</mt-cell>
		<mt-field label="开户名" v-model="bank_info.memberbank_truename"></mt-field>
		<mt-field label="开户行" v-model="bank_info.memberbank_name" v-if="bank_info.memberbank_type=='bank'"></mt-field>
		<mt-field label="账号" v-model="bank_info.memberbank_no"></mt-field>
		<mt-button class="ds-button-large mt-10" type="primary" v-on:click="submit">{{getSumitTitle}}</mt-button>
	</div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import { getBankInfo, addBank, editBank } from '../../../api/memberBank'
export default {
  components: {
  },
  computed: {
    isAddMode () {
      let mode = this.$route.query.action
      // add: 添加地址，edit: 编辑地址
      if (mode === 'add') {
        return true
      } else {
        return false
      }
    },
    getTitle () {
      if (this.isAddMode) {
        return '新增提现账户'
      } else {
        return '修改提现账户'
      }
    },
    getSumitTitle () {
      return '保存'
    }
  },
  data () {
    return {
      memberbank_type_options: [
        {
          label: '银行',
          value: 'bank'
        },
        {
          label: '支付宝',
          value: 'alipay'
        }
      ],
      memberbank_id: 0,
      bank_info: {
        memberbank_type: 'bank',
        memberbank_truename: '',
        memberbank_name: '',
        memberbank_no: ''
      }
    }
  },
  created: function () {
    if (!this.isAddMode) {
      this.memberbank_id = this.$route.query.memberbank_id
      getBankInfo(this.memberbank_id).then(res => {
        this.bank_info.memberbank_type = res.result.bank_info.memberbank_type
        this.bank_info.memberbank_truename = res.result.bank_info.memberbank_truename
        this.bank_info.memberbank_name = res.result.bank_info.memberbank_name
        this.bank_info.memberbank_no = res.result.bank_info.memberbank_no
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  },
  methods: {
    goBack () {
      this.$router.go(-1)
    },
    submit () {
      if (this.isAddMode) {
        Indicator.open()
        addBank(this.bank_info).then(
          (response) => {
            Indicator.close()
            this.$router.push({ name: 'MemberBankList' })
          }, (error) => {
            Indicator.close()
            Toast(error.message)
          })
      } else {
        Indicator.open()
        editBank(this.bank_info, this.memberbank_id).then(
          (response) => {
            Indicator.close()
            this.$router.push({ name: 'MemberBankList' })
          }, (error) => {
            Indicator.close()
            Toast(error.message)
          })
      }
    }
  }
}
</script>

<style lang="scss" scoped>
	.right-arrow{transform: rotate(-90deg);color:#ddd;font-size: .6rem;display: inline-block;}
	.input-wrap{position: relative;
		i{position: absolute;right:0;top:0;line-height: 2.4rem;display: block;width:2rem;text-align: center;font-size: 1rem}
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
</style>
