<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="发票管理" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
                <mt-button slot="right"  @click="goAdd">新增</mt-button>
            </mt-header>
        </div>
        <div v-if="invoice_list.length>0">
            <div v-for="item in invoice_list" :key="item.invoice_id">
                <div class="container">
                    <div class="top-wrapper">
                        <div v-if="item.invoice_state == 1">
                            <div class="title-wrapper">
                                <label class="title">普通发票</label>
                                <label class="title" >{{ item.invoice_title }}</label>
                            </div>
                            <label class="desc address-text" style="-webkit-box-orient:vertical">{{item.invoice_code}}</label>
                        </div>
                        <div v-else>
                            <div class="title-wrapper">
                                <label class="title">增值税发票</label>
                                <label class="title" >{{ item.invoice_company }}</label>
                            </div>
                            <label class="desc address-text" style="-webkit-box-orient:vertical">{{item.invoice_company_code}}</label>
                        </div>
                        <div class="bottom-line"></div>
                    </div>
                    <div class="bottom-wrapper">
                        <div class="bottom-left-wrapper" v-if="isFromCheckout">
                            <label class="subtitle" @click="useInvoice(item)">
                                <i v-if="invoice_id == item.invoice_id" class="iconfont">&#xe69d;</i><i v-else class="iconfont">&#xe69e;</i>使用
                            </label>
                        </div>
                        <div class="bottom-right-wrapper">
                            <div class="edit-wrapper" @click="onEdit(item.invoice_id)">
                                <label class="subtitle">编辑</label>
                            </div>
                            <div class="edit-wrapper delete-wrapper" @click="onDelete(item.invoice_id)">
                                <label class="subtitle">删除</label>
                            </div>
                        </div>

                    </div>
                </div>
            </div>
            <mt-button v-if="isFromCheckout" size="large" type="danger" @click="useInvoice(false)">不使用发票</mt-button>
        </div>
        <div  v-else>
            <empty-record></empty-record>
        </div>
    </div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import { getInvoiceList, delInvoice } from '../../../api/memberInvoice'
import EmptyRecord from '../../../components/EmptyRecord'
export default {
  name: 'InvoiceList',
  components: {
    EmptyRecord
  },
  data () {
    return {
      vat_deny: this.$route.query.vat_deny ? this.$route.query.vat_deny : false,
      isFromCheckout: this.$route.query.isFromCheckout ? this.$route.query.isFromCheckout : false,
      invoice_id: this.$route.query.invoice_id ? this.$route.query.invoice_id : 0,
      invoice_list: []
    }
  },
  created: function () {
    this.getInvoiceList()
  },

  methods: {
    goAdd () {
      this.$router.push({ name: 'MemberInvoiceForm', query: { action: 'add' } })
    },
    useInvoice (item) {
      let query = JSON.parse(this.$route.query.params)
      if (item == false) {
        query.invoice_id = 0
        query.invoice_content = '不需要发票'
      } else {
        let content = ''
        if (this.vat_deny) {
          if (item.invoice_state == 2) {
            Toast('订单商品不支持增值税发票')
            return
          }
        }
        if (item.invoice_state == 2) {
          content = '增值税发票 ' + item.invoice_company + ' ' + item.invoice_company_code + ' ' + item.invoice_reg_addr
        } else {
          content = '普通发票 ' + item.invoice_title + ' ' + item.invoice_code + ' ' + item.invoice_content
        }
        query.invoice_id = item.invoice_id
        query.invoice_content = content
      }
      this.$router.push({ name: 'MemberBuyStep1', query: query })
    },
    onEdit (invoiceId) {
      this.$router.push({ name: 'MemberInvoiceForm', query: { invoice_id: invoiceId } })
    },
    onDelete (invoiceId) {
      Indicator.open()
      delInvoice(invoiceId).then(
        (response) => {
          this.getInvoiceList()
          Indicator.close()
        }, (error) => {
          Indicator.close()
          Toast(error.message)
        })
    },
    getInvoiceList () {
      getInvoiceList().then(res => {
        this.invoice_list = res.result.invoice_list
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  }
}
</script>
<style scoped>
    .container{background:#fff}
    .top-wrapper {
        position: relative;
        flex: 1;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
    }
    .title-wrapper {
        height: 1rem;
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
        margin-top: 0.5rem;
        margin-left: 0.5rem;
    }
    .title {
        font-size: 0.8rem;
        color: #333;
        margin-left: 0.5rem;
    }
    .desc {
        color: #7c7f88;
        font-size: 0.7rem;
    }
    .address-text {
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        margin-left: 1rem;
        margin-right: 0.5rem;
    }
    .bottom-line {
        position: absolute;
        height: 1px;
        left: 0.5rem;
        bottom: 0;
        right: 0.5rem;
        background-color: #e8eaed;
    }
    .bottom-wrapper {
        height: 2.5rem;
        display: flex;
        flex-direction: row;
        justify-content: space-around;
        align-items: stretch;
    }
    .bottom-left-wrapper {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: center;
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
        margin-right: 0.5rem;
    }
    .subtitle {
        font-size: 0.7rem;
        color: #7c7f88;
        margin-top: 0.5rem;
        margin-bottom: 0.5rem;
        margin-left: 1rem;
        margin-right: 0.5rem;
    }
    .subtitle i{font-size:0.8rem;margin-right:.5rem;}
</style>
