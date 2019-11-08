<template>
    <div class="member-information">
      <div class="common-header-wrap">
        <mt-header title="退货详情" class="common-header">
          <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
      </div>
        <mt-cell title="退货编号" :value="refundReturn.refund_sn"></mt-cell>
        <mt-cell title="退货原因" :value="refundReturn.reason_info"></mt-cell>
        <mt-cell title="退货金额" :value="refundReturn.refund_amount"></mt-cell>
        <mt-cell title="退货说明" :value="refundReturn.buyer_message"></mt-cell>
        <div @click="isshow=true"><mt-cell title="退货凭证" :value="pic_list.length>0?'查看':''"></mt-cell></div>
        <mt-cell title="商家备注" v-if="refundReturn.seller_state" :value="refundReturn.seller_state"></mt-cell>
        <mt-cell title="平台备注" v-if="refundReturn.admin_message" :value="refundReturn.admin_message"></mt-cell>

        <mt-popup v-model="isshow" popup-transition="popup-fade" v-if="pic_list.length>0">
            <div class="preview-picture">
                <div class="picture-header"  v-on:click="isshow=false">
					<span>关闭</span><span v-if="pic_list">{{ defaultindex + 1 }} / {{ pic_list.length }}</span>
                </div>
                <div class="picture-body">
                    <mt-swipe
                            :auto="0"
                            :show-indicators="true"
                            :default-index="defaultindex"
                            class="ui-common-swiper"
                            :prevent="false"
                            :stop-propagation="true"
                            @change="handleChange"
                    >
                        <mt-swipe-item v-for="(item, index) in pic_list" v-bind:key="index">
                            <img v-bind:src="item" @click="setPopHeader()" />
                        </mt-swipe-item>
                    </mt-swipe>
                </div>
            </div>
        </mt-popup>
    </div>
</template>

<script>

import { Toast } from 'mint-ui'

import { getReturnInfo } from '../../../api/memberReturn'
export default {
  components: {
  },
  name: 'MemberReturnView',
  data () {
    return {
        refund_id: this.$route.query.refund_id,
      refundReturn: {},
      pic_list: [],
      isshow: false,
      defaultindex: 0
    }
  },
  mounted () {

  },
  computed: {

  },
  created: function () {
    getReturnInfo(this.refund_id).then(res => {
      this.refundReturn = res.result.return_info
      this.pic_list = res.result.pic_list
    }).catch(function (error) {
      Toast(error.message)
    })
  },

  methods: {
    /*
              handleChange: 轮播图改变时设置是否阻止事件冒泡
              @params: index 当前滑动的index
           */
    handleChange (index) {
      this.defaultindex = index
    }
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
                    left:0.8rem;
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
            bottom: 0;
            width: 100%;
            display: flex;
            justify-content: center;
            align-content: center;
            align-items: center;
        }
    }

</style>
