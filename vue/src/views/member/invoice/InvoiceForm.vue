<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header :title="getTitle" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <mt-cell title="发票类型">
            <mt-radio align="left" class="page-part" v-model="invoice.invoice_state" :options="invoice_state_options" />
        </mt-cell>
        <mt-tab-container v-model="invoice.invoice_state">
            <mt-tab-container-item id="1">
                <mt-field label="发票抬头" v-model="invoice.invoice_title"></mt-field>
                <mt-field label="纳税人识别号" v-model="invoice.invoice_code"></mt-field>
                <mt-field label="发票内容" v-model="invoice.invoice_content"></mt-field>
            </mt-tab-container-item>
            <mt-tab-container-item id="2">
                <mt-field label="单位名称" v-model="invoice.invoice_company"></mt-field>
                <mt-field label="纳税人识别号" v-model="invoice.invoice_company_code"></mt-field>
                <mt-field label="注册地址" v-model="invoice.invoice_reg_addr"></mt-field>
                <mt-field label="注册电话" v-model="invoice.invoice_reg_phone"></mt-field>
                <mt-field label="开户银行" v-model="invoice.invoice_reg_bname"></mt-field>
                <mt-field label="银行帐户" v-model="invoice.invoice_reg_baccount"></mt-field>
            </mt-tab-container-item>
        </mt-tab-container>
        <mt-button class="ds-button-large" type="primary" v-on:click="submit">{{getSumitTitle}}</mt-button>
    </div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import { getInvoiceInfo, addInvoice, editInvoice } from '../../../api/memberInvoice'
export default {
  name: 'InvoiceForm',
  data () {
    return {
      invoice_state_options: [
        {
          label: '普通发票',
          value: '1'
        },
        {
          label: '增值税专用发票',
          value: '2'
        }
      ],
      invoice_id: 0,
      invoice: {
        invoice_state: '1',
        invoice_title: '',
        invoice_code: '',
        invoice_content: '',
        invoice_company: '',
        invoice_company_code: '',
        invoice_reg_addr: '',
        invoice_reg_phone: '',
        invoice_reg_bname: '',
        invoice_reg_baccount: ''
      }
    }
  },
  created: function () {
    if (!this.isAddMode) {
      this.invoice_id = this.$route.query.invoice_id
      getInvoiceInfo(this.invoice_id).then(res => {
        this.invoice.invoice_state = res.result.invoice_state
        this.invoice.invoice_title = res.result.invoice_title
        this.invoice.invoice_code = res.result.invoice_code
        this.invoice.invoice_content = res.result.invoice_content
        this.invoice.invoice_company = res.result.invoice_company
        this.invoice.invoice_company_code = res.result.invoice_company_code
        this.invoice.invoice_reg_addr = res.result.invoice_reg_addr
        this.invoice.invoice_reg_phone = res.result.invoice_reg_phone
        this.invoice.invoice_reg_bname = res.result.invoice_reg_bname
        this.invoice.invoice_reg_baccount = res.result.invoice_reg_baccount
      }).catch(function (error) {
        Toast(error.message)
      })
    }
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
        return '新增发票'
      } else {
        return '修改发票'
      }
    },
    getSumitTitle () {
      let isFromCheckout = this.$route.query.isFromCheckout
      if (isFromCheckout) {
        return '保存并使用'
      } else {
        return '保存'
      }
    }
  },
  methods: {
    goBack () {
      this.$router.go(-1)
    },
    submit () {
      // 普通发票
      if (this.invoice.invoice_state === '1') {
        if (this.invoice.invoice_title === '') {
          Toast('请填写发票抬头')
          return
        }
        if (this.invoice.invoice_code === '') {
          Toast('请填写纳税人识别号')
          return
        }
        if (this.invoice.invoice_content === '') {
          Toast('请填写发票内容')
          return
        }
      }
      // 专用发票
      if (this.invoice.invoice_state === '2') {
        if (this.invoice.invoice_company === '') {
          Toast('请填写单位名称')
          return
        }
        if (this.invoice.invoice_company_code === '') {
          Toast('请填写纳税人识别号')
          return
        }
        if (this.invoice.invoice_reg_addr === '') {
          Toast('请填写注册地址')
          return
        }
        if (this.invoice.invoice_reg_phone === '') {
          Toast('请填写注册电话')
          return
        }
        if (this.invoice.invoice_reg_bname === '') {
          Toast('请填写开户银行')
          return
        }
        if (this.invoice.invoice_reg_baccount === '') {
          Toast('请填写银行帐户')
          return
        }
      }

      if (this.isAddMode) {
        Indicator.open()
        addInvoice(this.invoice).then(
          (response) => {
            Indicator.close()
            this.updateSelectedInvoice()
          }, (error) => {
            Indicator.close()
            Toast(error.message)
          })
      } else {
        Indicator.open()
        editInvoice(this.invoice, this.invoice_id).then(
          (response) => {
            Indicator.close()
            this.updateSelectedInvoice()
          }, (error) => {
            Indicator.close()
            Toast(error.message)
          })
      }
    },
    updateSelectedInvoice () {
      let isFromCheckout = this.$route.query.isFromCheckout
      let goBackLevel = this.$route.query.goBackLevel ? this.$route.query.goBackLevel : -1
      if (isFromCheckout) {
        this.$router.go(goBackLevel)
      } else {
        this.goBack()
      }
    }
  }

}
</script>

<style scoped>
.ds-button-large{margin-top:1rem;}
.mint-radiolist {
		display: flex;
               
	}
.mint-radiolist >>> .mint-radio-label {font-size: 13px}  
</style>
