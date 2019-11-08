<template>
    <div class="member-information">
        <div class="common-header-wrap">
            <mt-header title="新增退款" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div class="form">
            <mt-field label="退款说明" v-model="buyer_message" type="textarea" />
            <mt-cell title="退款凭证" ></mt-cell>
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
    </div>
</template>

<script>

import { Toast } from 'mint-ui'

import { uploadRefundImage, addRefundAll } from '../../../api/memberRefund'
export default {
  components: {

  },
  name: 'MemberRefundForm',
  data () {
    return {
      order_id: this.$route.query.order_id,
      image: ['', '', ''],
      buyer_message: '',
      file_value: ['', '', '']
    }
  },
  mounted () {
  },
  computed: {
  },
  created: function () {
  },
  methods: {
    submitInformation () {
      addRefundAll(this.order_id, this.buyer_message, this.file_value).then(res => {
        Toast(res.message)
        this.$router.push({ name: 'MemberRefundList' })
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
    .image-wrapper{display:flex;margin:1rem;}
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
      .ds-button-large{margin-top: 1rem;}
  }

</style>
