<template>
	<div class="distributor-bank-list">
		<div class="common-header-wrap">
			<mt-header title="提现账户" class="common-header">
				<mt-button slot="left" icon="back" @click="goBack"></mt-button>
				<mt-button slot="right"  @click="goAdd">新增</mt-button>
			</mt-header>
		</div>
		<div v-if="bank_list.length>0">
			<div v-for="item in bank_list" :key="item.bank_id">
				<div class="container">
					<div class="top-wrapper">
						<div class="title-wrapper">
							<label class="title">{{ item.memberbank_name }}</label>
							<label class="title">{{ item.memberbank_truename }}</label>
						</div>
						<label class="desc bank-text" style="-webkit-box-orient:vertical">{{memberbankTypeText(item.memberbank_type)}}&nbsp;{{item.memberbank_no}}</label>
						<div class="bottom-line"></div>
					</div>
					<div class="bottom-wrapper">
						<div class="bottom-right-wrapper">
							<div class="edit-wrapper" @click="onEdit(item.memberbank_id)">
								<label class="subtitle">编辑</label>
							</div>
							<div class="edit-wrapper delete-wrapper" @click="onDelete(item.memberbank_id)">
								<label class="subtitle">删除</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div  v-else>
			<empty-record></empty-record>
		</div>

	</div>
</template>

<script>

import { Toast, Indicator, MessageBox } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getBankList, delBank } from '../../../api/memberBank'
export default {
  components: {
    EmptyRecord
  },
  name: 'MemberBankList',
  data () {
    return {
      bank_list: []
    }
  },
  computed: {
    memberbankTypeText () {
      return function (memberbankType) {
        if (memberbankType === 'bank') {
          return '银行'
        } else if (memberbankType === 'alipay') {
          return '支付宝'
        } else {
          return '账户类型错误'
        }
      }
    }
  },
  created: function () {
    this.getBankList()
  },
  methods: {
    goBack () {
      this.$router.go(-1)
    },
    goAdd () {
      this.$router.push({ name: 'MemberBankForm', query: { action: 'add' } })
    },
    onEdit (memberbankId) {
      this.$router.push({ name: 'MemberBankForm', query: { memberbank_id: memberbankId } })
    },
    onDelete (memberbankId) {
      MessageBox.confirm('确定要删除该提现账户吗？').then(action => {
        Indicator.open()
        delBank(memberbankId).then(
          (response) => {
            this.getBankList()
            Indicator.close()
          }, (error) => {
            Indicator.close()
            Toast(error.message)
          })
      })
    },
    getBankList () {
      getBankList().then(res => {
        this.bank_list = res.result.bank_list
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  }
}
</script>
<style  lang="scss" scoped>
	.distributor-bank-list {
		.container {
			display: flex;
			flex-direction: column;
			justify-content: flex-start;
			align-items: stretch;
			background-color: #fff;
			border-bottom: 1px solid #e8eaed;
		}
		.top-wrapper {
			position: relative;
			flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: flex-start;
			align-items: stretch;
		}
		.title-wrapper {
			height:1rem;
			display: flex;
			flex-direction: row;
			justify-content: flex-start;
			align-items: center;
			margin-top:0.5rem;
			margin-left:0.5rem;
		}
		.title {
			font-size:0.8rem;
			color: #333;
			margin-left:0.5rem;
		}
		.default {
			width:1.4rem;
			margin-left:0.5rem;
			margin-right:0.5rem;
			border: 1px solid #e93b3d;
			color: $primaryColor;
			font-size:0.5rem;
			text-align: center;
			border-radius:0.1rem;
		}
		.desc {
			color: #7c7f88;
			font-size: 0.7rem;
		}
		.bank-text {
			margin-top:0.5rem;
			margin-bottom:0.5rem;
			margin-left:1rem;
			margin-right:0.5rem;
		}
		.bottom-line {
			position: absolute;
			height: 1px;
			left:0.5rem;
			bottom: 0;
			right:0.5rem;
			background-color: #e8eaed;
		}
		.bottom-wrapper {
			height:2.5rem;
			display: flex;
			flex-direction: row;
			justify-content: space-around;
			align-items: stretch;
		}
		.bottom-right-wrapper {
			flex: 1;
			display: flex;
			flex-direction: row;
			justify-content: flex-end;
			align-items: stretch;
		}
		.edit-wrapper {
			display: flex;
			flex-direction: row;
			justify-content: flex-start;
			align-items: center;
		}
		.delete-wrapper {
			margin-right:0.5rem;
		}
		.indicator {
			width:1rem;
			height:1rem;
			margin-left:0.75rem;
			margin-right:0.5rem;
		}
		.icon {
			width:0.9rem;
			height:0.9rem;
			margin-left:0.5rem;
		}
		.subtitle {
			font-size:0.7rem;
			color: #7c7f88;
			margin-top:0.5rem;
			margin-bottom:0.5rem;
			margin-left: 1rem;
			margin-right:0.5rem;
		}
		.subtitle i{font-size:0.8rem;margin-right:.5rem;}
	}

</style>
