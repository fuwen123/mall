<template>
    <div class="member-information">
        <div class="common-header-wrap">
            <mt-header :title="'新增'+title" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>

        <div class="form">
            <div @click="popupReason=true"><mt-cell class="menu-item" :title="title+'原因'" is-link >{{reason_name}}</mt-cell></div>
            <mt-field class="menu-item" label="退款金额" v-model="refund_amount" :placeholder="'最多可退金额'+goods.goods_pay_price+'元'" type="text" />
            <mt-field v-if="type==2" class="menu-item" label="退货数量" v-model="goods_num" :placeholder="'最多可退数量'+goods.goods_num+'件'" type="text" />
            <mt-field class="menu-item" :label="title+'说明'" v-model="buyer_message" type="textarea" />
            <mt-cell class="menu-item" :title="title+'凭证'" ></mt-cell>
            <div class="image-wrapper">
                <div class="user-avatar iconfont icon-xiangji" ref="upload_btn1" @change="uploadRefundImage(0,$event)">
                    <img v-if="image[0]" :src="image[0]">
                    <input type="file" accept="image/jpg,image/png,image/gif,image/bmp,image/jpeg">
                </div>
                <div class="user-avatar iconfont icon-xiangji" ref="upload_btn2" @change="uploadRefundImage(1,$event)">
                    <img v-if="image[1]" :src="image[1]">
                    <input type="file" accept="image/jpg,image/png,image/gif,image/bmp,image/jpeg">
                </div>
                <div class="user-avatar iconfont icon-xiangji" ref="upload_btn3" @change="uploadRefundImage(2,$event)">
                    <img v-if="image[2]" :src="image[2]">
                    <input type="file" accept="image/jpg,image/png,image/gif,image/bmp,image/jpeg">
                </div>
            </div>
            <mt-button class="ds-button-large" type="primary" @click="submitInformation">保存</mt-button>
        </div>

        <mt-popup v-model="popupReason" position="right" class="common-popup-wrapper">
            <div class="common-header-wrap">
                <mt-header :title="title+'原因'" class="common-header">
                    <mt-button slot="left" icon="back" @click="popupReason=false"></mt-button>
                </mt-header>
            </div>
            <div class="common-popup-content">
                <mt-radio
                        v-model="reason_id"
                        :options="reason_options">
                </mt-radio>
            </div>
        </mt-popup>
    </div>
</template>

<script>

import { Toast } from 'mint-ui'

import { uploadRefundImage, getCommonData, addRefund } from '../../../api/memberRefund'
export default {
  components: {

  },
  name: 'MemberReturnForm',
  data () {
    return {
      type: this.$route.query.type,
      title: this.$route.query.type == 2 ? '退货' : '退款',
      order_id: this.$route.query.order_id,
      order_goods_id: this.$route.query.order_goods_id,
      image: ['', '', ''],
      buyer_message: '',
      file_value: ['', '', ''],
      reason_id: '',
      reason_name: '',
      popupReason: false,
      reason_list: {},
      reason_options: [],
      refund_amount: '',
      goods_num: '',
      goods: {}
    }
  },
  mounted () {

  },
  computed: {

  },
  created: function () {
    getCommonData(this.order_id, this.order_goods_id).then(res => {
      this.goods = res.result.goods
      let reason_options = res.result.reason_list

      for (var i in reason_options) {
        this.reason_options.push({
          label: reason_options[i].reason_info,
          value: reason_options[i].reason_id + ''
        })
        this.reason_list[reason_options[i].reason_id + ''] = reason_options[i].reason_info
        if (this.reason_id === '') {
          this.reason_id = reason_options[i].reason_id + ''
        }
      }
    }).catch(function (error) {
      Toast(error.message())
    })
  },
  watch: {
    reason_id: function (reason_id) {
      this.reason_name = this.reason_list[reason_id]

      this.popupReason = false
    }

  },
  methods: {

    submitInformation () {
      if (!this.refund_amount) {
        Toast('请填写退款金额')
        return
      }
      addRefund(this.type, this.order_id, this.order_goods_id, this.refund_amount, this.goods_num, this.reason_id, this.buyer_message, this.file_value).then(res => {
        Toast(res.message)
        if (this.type == 2) {
          this.$router.push({ name: 'MemberReturnList' })
        } else {
          this.$router.push({ name: 'MemberRefundList' })
        }
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
