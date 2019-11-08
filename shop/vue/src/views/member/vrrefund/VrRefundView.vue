<template>
    <div class="member-information">
        <div class="common-header-wrap">
            <mt-header title="退款详情" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <mt-cell title="退款编号" :value="refund.refund_sn"></mt-cell>
        <mt-cell title="退款兑换码" :value="code_array.join(',')"></mt-cell>
        <mt-cell title="退款金额" :value="refund.refund_amount"></mt-cell>
        <mt-cell title="退款说明" :value="refund.buyer_message"></mt-cell>
        <mt-cell title="平台备注" v-if="refund.admin_message" :value="refund.admin_message"></mt-cell>

    </div>
</template>

<script>

import { Toast } from 'mint-ui'

import { getVrRefundInfo } from '../../../api/memberVrRefund'
export default {
  components: {

  },
  name: 'MemberVrRefundView',
  data () {
    return {
      refund_id: this.$route.query.refund_id,
      refund: {},
        code_array:[],
    }
  },
  mounted () {

  },
  computed: {

  },
  created: function () {
    getVrRefundInfo(this.refund_id).then(res => {
      this.refund = res.result.refund
      this.code_array = res.result.code_array
    }).catch(function (error) {
      Toast(error.message)
    })
  },

  methods: {

  }
}
</script>
<style  lang="scss" scoped>

    .swipe-wrapper {
        width: 100%;
    }
    .mint-popup {
        width: 100%;
        height: 100%;
        background-color: #000;
    }
    .mint-swipe,
    .mint-swipe-items-wrap {
        position: static;
    }
    .preview-picture {
        width: 100%;
        height: 100%;
        position: fixed;
        z-index: 10;
        top: 0;
        bottom: 0;
        left: 0;
        right: 0;
        background-color: #000;
        .picture-header {
            height:2.2rem;
            color: #000;
            background-color: #fff;
            display: flex;
            justify-content: center;
            align-content: center;
            align-items: center;
            width: 100%;
            top: 0;
            span {
                font-size:0.7rem;
                font-weight: normal;
                &:first-child {
                    cursor: pointer;
                    position: absolute;
                    left:0.75rem;
                    background-size:1.2rem;
                    display: inline-block;
                    height:2.2rem;
                    line-height:2.2rem;
                }
            }
        }
        .picture-body {
            position: absolute;
            top:2.2rem;
            bottom:0;
            width: 100%;
            display: flex;
            justify-content: center;
            align-content: center;
            align-items: center;
        }
    }

</style>
