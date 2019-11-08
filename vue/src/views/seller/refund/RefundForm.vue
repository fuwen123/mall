<template>
    <div class="member-information">
      <div class="common-header-wrap">
        <mt-header :title="'处理'+title" class="common-header">
          <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
      </div>

      <div class="form">
          <mt-cell :title="'同意'+title">
              <mt-radio
                      v-model="seller_state"
                      :options="seller_state_options">
              </mt-radio>
          </mt-cell>
          <mt-cell v-if="refund_type==2" title="弃货">
              <mt-radio
                      v-model="return_type"
                      :options="return_type_options">
              </mt-radio>
          </mt-cell>
        <mt-field class="menu-item" label="备注信息" v-model="seller_message" type="textarea" />

        <mt-button class="ds-button-large" type="primary" @click="submitInformation">保存</mt-button>
      </div>
    </div>
</template>

<script>

import { Toast } from 'mint-ui'

import { editRefund } from '../../../api/sellerRefund'
export default {
  components: {

  },
  name: 'SellerRefundForm',
  data () {
    return {
      refund_id: this.$route.query.refund_id,
      seller_message: '',
      seller_state: '2',
      refund_type: this.$route.query.refund_type,
      title: this.$route.query.refund_type == 2 ? '退货' : '退款',
      return_type: '2',
      return_type_options: [
        {
          label: '是',
          value: '1'
        },
        {
          label: '否',
          value: '2'
        }
      ],
      seller_state_options: [
        {
          label: '是',
          value: '2'
        },
        {
          label: '否',
          value: '3'
        }
      ]
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
      editRefund(this.refund_id, this.return_type, this.seller_message, this.seller_state).then(res => {
        Toast(res.message)
        this.$router.go(-1)
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

    .menu-item {
      border-top: 1px solid #eee
    }
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
