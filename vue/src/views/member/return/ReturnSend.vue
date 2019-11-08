<template>
    <div class="member-information">
      <div class="common-header-wrap">
        <mt-header title="退货" class="common-header">
          <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
      </div>
        <div class="cart-subtotal">
            <div @click="popupExpress=true"><mt-cell class="menu-item" title="物流公司" >{{express_name}}<i class="indicator iconfont">&#xe650;</i></mt-cell></div>
            <div class="message" v-if="shipping_express_id!='0'">
                <mt-field label="物流单号" type="text" v-model="shipping_code"></mt-field>
            </div>
        </div>
        <mt-button class="ds-button-large" type="primary" @click="submitInformation">保存</mt-button>

    <mt-popup v-model="popupExpress" position="right" class="common-popup-wrapper">
        <div class="common-header-wrap">
            <mt-header title="物流公司" class="common-header">
                <mt-button slot="left" icon="back" @click="popupExpress=false"></mt-button>
            </mt-header>
        </div>
        <div class="common-popup-content">
            <mt-radio
                    v-model="shipping_express_id"
                    :options="express_options">
            </mt-radio>
        </div>
    </mt-popup>
    </div>
</template>

<script>

import { Toast } from 'mint-ui'

import { getExpress, sendReturn } from '../../../api/memberReturn'
export default {
  components: {

  },
  name: 'MemberReturnSend',
  data () {
    return {
      shipping_code: '',
      shipping_express_id: '',
      express_name: '',
      popupExpress: false,
      express_list: {},
      express_options: [],
      refund_id: this.$route.query.refund_id
    }
  },
  mounted () {

  },
  computed: {

  },
  created: function () {
    getExpress().then(res => {
      let express_options = res.result.express_list

      for (var i in express_options) {
        this.express_options.push({
          label: express_options[i].express_name,
          value: express_options[i].express_id + ''
        })
        this.express_list[express_options[i].express_id + ''] = express_options[i]
        if (this.shipping_express_id == '') {
          this.shipping_express_id = express_options[i].express_id + ''
        }
      }
    }).catch(function (error) {
      Toast(error.message())
    })
  },
  watch: {
    shipping_express_id: function (shipping_express_id) {
      this.express_name = this.express_list[shipping_express_id].express_name

      this.popupExpress = false
    }

  },
  methods: {

    submitInformation () {
      sendReturn(this.refund_id, this.shipping_express_id, this.shipping_code).then(res => {
        this.$router.go(-1)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    uploadRefundImage (index, event) {
      if (typeof (event.target.files[0]) === 'undefined') {
        return
      }
      let formdata = new FormData()

      formdata.append('refund_pic', event.target.files[0])

      uploadRefundImage(formdata).then(res => {
        this.image.splice(index, 1, res.result.pic + '?' + Math.floor(Math.random() * 100))
        this.file_value.splice(index, 1, res.result.file_name)
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  }
}
</script>
<style  lang="scss" scoped>
  .member-information {
    background: #fff;
    .image-wrapper{display:flex}
    .user-avatar {
    flex:1;
    border:1px solid #eee;
      position: relative;
      width: 5rem;
      height: 5rem;
      margin: 0 auto;
      text-align: center;
      img {
        width: 100%;
        height: 100%
      }
      input {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        opacity: 0
      }
    }
    .user-avatar::before {
      position: absolute;
      font-size: 1.5rem;
      line-height: 5rem;
      left: 50%;
      margin-left: -.75rem;
      color: rgba(0, 0, 0, 0.5)
    }
    .menu-item {
      border-top: 1px solid #eee
    }
  }

</style>
