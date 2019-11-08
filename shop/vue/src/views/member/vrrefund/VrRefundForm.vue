<template>
    <div class="member-information">
        <div class="common-header-wrap">
            <mt-header title="新增退款" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div class="form">
            <mt-field label="退款说明" v-model="buyer_message" type="textarea" />
            <div @click="codeVisible=true">
                <mt-cell title="可退款兑换码" is-link />
            </div>
            <mt-button class="ds-button-large" type="primary" @click="submitInformation">保存</mt-button>
        </div>
        <mt-popup v-model="codeVisible" position="right" class="common-popup-wrapper">
            <div class="common-header-wrap">
                <mt-header title="可退款兑换码" class="common-header">
                    <mt-button slot="left" icon="back" @click="codeVisible=false"></mt-button>
                </mt-header>
            </div>
            <div class="common-popup-content">
                <mt-checklist
                        v-model="code"
                        :options="code_options">
                </mt-checklist>
            </div>
        </mt-popup>
    </div>
</template>

<script>

import { Toast } from 'mint-ui'

import { getCommonData, addVrRefund } from '../../../api/memberVrRefund'
export default {
  components: {

  },
  name: 'MemberVrRefundForm',
  data () {
    return {
      codeVisible: false,
      code: [],
      code_options: [],
      order_id: this.$route.query.order_id,
      buyer_message: '',
    }
  },
  mounted () {
  },
  computed: {
  },
  created: function () {
    getCommonData(this.order_id).then(res => {
      let code_list = res.result.code_list
      for (var i in code_list) {
        this.code_options.push({
          label: code_list[i].vr_code,
          value: code_list[i].rec_id
        })
      }
    }).catch(function (error) {
      Toast(error.message)
    })
  },
  methods: {
    submitInformation () {
      addVrRefund(this.order_id, this.code, this.buyer_message).then(res => {
        Toast(res.message)
        this.$router.push({ name: 'MemberVrRefundList' })
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
