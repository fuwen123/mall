<!-- OrderDetailBody.vue -->
<template>
    <div>
        <div class="common-header-wrap">
            <mt-header title="订单详情" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div
                class="order-body"
                v-if="order_info"
        >
            <div class="image">
                <span>{{order_info.order_state_text}}</span>
            </div>

            <div class="address">
                <div>
                    <span class="mobile">接收手机：{{ order_info.buyer_phone }}</span>
                </div>
                <div v-if="order_info.code_list.length" class="vr_code">
                    <span v-for="(item ,index) in order_info.code_list" :key="index">
                        <span>兑换码{{item.vr_code}}</span><br><span class="state">{{item.vr_code_desc}}</span>
                    </span>
                </div>
            </div>

            <div
                    class="containers"
            >

                <img
                        class="photo"
                        v-bind:src="order_info.goods_image_url"
                />
                <div class="right-wrapper">
                    <label class="title">{{ order_info.goods_name }}</label>
                    <label class="property">{{ order_info.goods_spec }}</label>
                    <div class="desc-wrapper">
                        <div>
                            <label class="price"
                            >￥ {{ order_info.goods_price }}</label
                            >
                            <label class="count">x{{ order_info.goods_num }}</label>
                        </div>
                        <!--<div class="btn-list">-->
                            <!--<mt-button v-if="order_info.if_complain" plain size="small" v-on:click="complaint(order_info.order_id,order_info.rec_id)">投诉</mt-button>-->
                            <!--<mt-button v-if="order_info.refund=='1'" plain size="small" v-on:click="showRefund(order_info.rec_id)">售后</mt-button>-->
                        <!--</div>-->
                    </div>
                </div>
            </div>

            <div class="detail">
                <div class="number">
                    <label
                    >订单编号：{{ order_info.order_sn }}
                    </label>
                    <p>
                        下单时间：{{order_info.add_time}}
                    </p>
                </div>
                <div class="pay" v-if="order_info.payment_name">
                    <p>支付方式：{{ order_info.payment_name }}</p>
                </div>

            </div>

            <div
                    class="desc section-header section-footer"
            >


                <div class="container" v-if="order_info.promotion && order_info.promotion.length > 0">
                    <label class="title">优惠</label>
                    <label class="subtitle" v-for="(p,i) in order_info.promotion" v-bind:key="i">{{p[1]}}}</label>
                </div>
                <label class="amount"
                >实付款 : <span> ￥{{ order_info.order_amount }}</span>
                </label>
            </div>

        </div>


        <mt-popup v-model="popupRefund" position="right" class="common-popup-wrapper">
            <div class="common-header-wrap">
                <mt-header title="售后" class="common-header">
                    <mt-button slot="left" icon="back" @click="popupRefund=false"></mt-button>
                </mt-header>
            </div>
            <div class="common-popup-content">
                <div @click='refund(1)'><mt-cell class="menu-item" title="退款" is-link></mt-cell></div>
                <div @click='refund(2)'><mt-cell class="menu-item" title="退货" is-link></mt-cell></div>
            </div>
        </mt-popup>
    </div>
</template>

<script>

import { Toast, MessageBox } from 'mint-ui'
import { getOrderInfo } from '../../../api/sellerVrOrder'
export default {
  name: 'SellerOrderDetail',
  data () {
    return {
      order_info: {},
        rec_id:0,
        popupRefund:false,
    }
  },

  created: function () {
    if (this.$route.query.order_id) {
      getOrderInfo(this.$route.query.order_id).then(res => {
        this.order_info = res.result.order_info
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  },

  methods: {
      showRefund(rec_id){
          this.rec_id=rec_id
          this.popupRefund=true
      },
      refund (type) {
          this.$router.push({ name: 'MemberReturnForm', query: {type:type, order_id: this.order_info.order_id, order_goods_id: this.rec_id } })
      },

    complaint (order_id, goods_id) {
      this.$router.push({ name: 'MemberComplaintForm', query: { order_id: order_id, goods_id: goods_id } })
    }
  }
}
</script>
<style lang="scss" scoped>
    .order-body {
        overflow: auto;
        height: 100%;
        position: absolute;
        width: 100%;
        .desc{
            .container {
                display: flex;
                flex-direction: row;
                justify-content: flex-start;
                align-items: center;
                background-color: #fff;
            }
            .title {
                width:5rem;
                font-size:0.7rem;
                color: #333;
                margin-left:0.6rem;
            }
            .subtitle {
                flex: 1;
                margin-left:1rem;
                margin-right:0.6rem;
                color: $primaryColor;
                font-size:0.7rem;
                text-align: right;
            }
        }

    }
    .image {
        background:#e93b3d;
        height: 3.5rem;
        display: flex;
        justify-content: flex-start;
        align-items: center;
        img {
            height: 0.9rem;
            padding:0 0.6rem;
        }
        span {
            font-size: 0.85rem;
            color: #fff;
            padding-left: 0.5rem;
        }
    }
    .receipt {
        display: flex;
        justify-content: space-between;
        align-items: center;
        height: auto;
        padding: 0.65rem;
        background-color: #fff;
        margin-bottom: 0.4rem;
        label {
            display: flex;
            align-items: center;
        }
        img {
            height: 0.8rem;
            margin:0 0.75rem 0 0.5rem;
        }
        .arrow {
            width:0.25rem;
            height: 0.5rem;
        }
        span {
            font-size: 0.7rem;
            color: #333;
        }
    }
    .containers {
        display: flex;
        flex-direction: row;
        justify-content: flex-start;
        align-items: stretch;
        background-color: #fff;
        border-bottom: 1px solid #e8eaed;
    }
    .onClick {
        height: 2.2rem;
        line-height: 2.2rem;
        text-align: center;
        background-color: #fff;
        p {
            font-size: 0.7rem;
            color: #333;
        }
    }
    .photo {
        width: 4rem;
        height: 4rem;
        margin: 0.75rem 0.5rem 0.75rem 0.75rem;
        border: 1px solid #e8eaed;
        flex-basis: 4rem;
        flex-shrink: 0;
    }
    .right-wrapper {
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        padding:0 0.75rem 0 0;
        width: 100%;
        overflow: hidden;
    }
    .title {
        margin-top:0.7rem;
        color: #333;
        font-size:0.7rem;
        overflow: hidden;
        text-overflow: ellipsis;
        white-space: nowrap;
    }
    .property {
        font-size:0.7rem;
        color: #7c7f88;
        padding-top:0.5rem;
    }
    .count {
        margin-top:0.2rem;
        color: #7c7f88;
        font-size:0.7rem;
        margin-right:0.5rem;
    }
    .desc-wrapper {
        height:1rem;
        display: flex;
        flex-direction: row;
        justify-content: space-between;
        align-items: center;
        padding-top:1rem;
    }
    .propertyOrder {
        padding-top:1.7rem;
    }
    .price {
        color: $primaryColor;
        font-size:0.8rem;
        margin-left: 0;
    }
    .count {
        color: #7c7f88;
        font-size:0.8rem;
        margin-left:0.5rem;
    }
    .address {
        padding-bottom:0.5rem;
        background-color: #fff;
        margin-bottom:0.5rem;
        div {
            padding:0.5rem 0.5rem 0;
        }
        img {
            height: 0.8rem;
        }
        span {
            color: #333;
            font-size: 0.8rem;
            &.mobile {
                /*padding-left: 1.05rem;*/
            }
        }
        p {
            margin:0.25rem 0.9rem 0.55rem 0.5rem;
            font-size: 0.7rem;
            color: #7c7f88;

            overflow: hidden;
            text-overflow: ellipsis;
            display: -webkit-box;
            -webkit-box-orient: vertical;
            -webkit-line-clamp: 2;
        }
    }

    .contact {
        display: flex;
        justify-content: flex-end;
        align-items: center;
        height: 2.3rem;
        background-color: #fff;
        margin-top: 0.4rem;
        border-bottom: 1px solid #e8eaed;
        padding: 0 0.65rem;
        span {
            font-size: 0.6rem;
            color: #333;
            padding-right: 0.3rem;
        }
        img {
            width: 0.6rem;
            height: 0.65rem;
        }
    }

    .detail {
        display: flex;
        flex-direction: column;
        font-size: 0.7rem;
        color: #7c7f88;
        background-color: #fff;
        margin: 0.4rem 0;
        box-sizing: border-box;
        .number {
            padding: 0.7rem 0.8rem;
            border-bottom: 1px solid #e8eaed;
            p {
                padding-top: 0.3rem;
                font-size: 0.7rem;
            }
            button {
                color: #7c7f88;
                height: 1rem;
                background-color: #fff;
                border: 1px solid #7c7f88;
                width: 2.7rem;
                height: 1rem;
                border-radius: 0.1rem;
                font-size: 0.7rem;
            }
        }
        .pay {
            border-bottom: 1px solid #e8eaed;
            padding: 0.7rem 0.8rem;
        }
        .givetime {
            padding: 0.7rem 0.8rem;
            font-size: 0.7rem;
        }
        input {
            background-color: #fff;
            border: 1px solid #7c7f88;
        }
    }
    .desc {
        background-color: #fff;
        display: flex;
        flex-direction: column;
        justify-content: flex-start;
        align-items: stretch;
        /*padding-top: 0.6rem;*/
        box-sizing: border-box;
        margin-bottom: 4rem;
        .desc-item {
            flex: 1;
            margin-top: 0.5rem;
        }
        .amount {
            display: flex;
            justify-content: flex-end;
            font-size: 0.7rem;
            color: #333;
            padding-right: 0.75rem;
            border-top: 1px solid #e8eaed;
            /*margin-top: 0.6rem;*/
            height: 2.25rem;
            line-height: 2.25rem;
            span {
                font-size: 0.8rem;
                color: $primaryColor;
            }
        }
    }
    .btn {
        position: fixed;
        bottom: 0;
        right: 0;
        width: 100%;
        height: 2.7rem;
        display: flex;
        justify-content: flex-end;
        background-color: #fff;
        align-items: center;
        border-top: 1px solid #e8eaed;
        button {
            width: 4.1rem;
            height: 1.8rem;
            font-size: 0.7rem;
            margin-right: 0.35rem;
            background-color: #fff;
            border: 1px solid #ccc;
        }
        .buttonbottom {
            color: $primaryColor;
            border: 1px solid #e93b3d;
        }
        .mint-popup {
            width: 100%;
            height: 11.75rem;
        }
        .cancels {
            height: 100%;
            .cancelInfo {
                display: flex;
                flex-wrap: nowrap;
                justify-content: space-between;
                border-bottom: 1px solid #f0f0f0;
                span {
                    color: #000;
                    font-size: 0.7rem;
                }
                .cancel {
                    padding: 0.5rem 0.75rem;
                }
                .success {
                    padding: 0.5rem 0.75rem;
                }
            }
            .reason {
                margin-top: 0.5rem;
                p {
                    height: 0.8rem;
                    line-height: 0.8rem;
                    text-align: center;
                    padding: 0.5rem;
                    &:hover {
                        color: #e93b3d;
                    }
                }
            }
        }
    }
    .ship {
        margin-bottom:0;
    }
    .vr_code > span{border-top:1px solid #e1e1e1;display: block;padding-top:0.5rem;padding-bottom:0.5rem}
    .vr_code > span:last-child{padding-bottom:0}
    .vr_code .state{font-size:.7rem;color:#7c7f88}
</style>

<!-- 字体图标样式覆盖 -->
<style>
    .mint-toast-icon {
        font-size: 1.9rem;
    }
    button {
        padding: 0;
    }
</style>
