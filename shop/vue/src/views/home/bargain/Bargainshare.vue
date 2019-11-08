<template>
    <div class="whole-wrapper">
    <div class="bargain-share" :class="flag?'':'inactive'" v-if="bargainorder_info">
        <div class="bargain-info" @click="goGoods">
            <div class="member_avatar"><img :src="bargainorder_info.bargainorder_initiator_avatar"/></div>
            <div class="member_name">{{bargainorder_info.bargainorder_initiator_name}}</div>
            <div class="bargain_remark" v-if="bargainorder_info.bargain_remark">[{{bargainorder_info.bargain_remark}}]</div>
            <div class="goods_info">
                <div class="p_img"><img :src="bargainorder_info.bargain_goods_image_url"/></div>
                <div class="p_info">
                    <div class="goods_name">{{bargainorder_info.bargain_goods_name}}</div>
                    <div class="bargain_goods_price">￥<em>{{bargainorder_info.bargainorder_current_price}}</em><del>{{bargainorder_info.bargain_goods_price}}</del></div>
                </div>
            </div>
            <div v-if="!flag" class="expired">砍价已过期</div>
        </div>
        <div class="bargain-order" v-if="flag">
            <div class="connect-wrapper">
                <div class="top-dot dot"></div>
                <div class="bottom-dot dot"></div>
                <div class="round"></div>
            </div>
            <div class="connect-wrapper right">
                <div class="top-dot dot"></div>
                <div class="bottom-dot dot"></div>
                <div class="round"></div>
            </div>
            <div class="count">已砍{{bargainorder_info.bargain_goods_diff_price}}元</div>
            <div class="progress-wrapper mt-10">
                <div class="progress-back"><div class="progress-front" :style="{width:bargainorder_info.bargain_percent+'%'}"><div class="arrow"></div></div></div>
            </div>
            <div class="count-down-wrapper mt-10">
                砍价还剩<span class="block">{{hours}}</span>:<span class="block">{{minute}}</span>:<span class="block">{{second}}</span>结束
            </div>
            <mt-button type="primary" class="button-1 mt-10 mb-5" :class="(bargainorder_info.bargain_if_add && (bargainorder_info.bargain_total>bargainorder_info.bargainorder_times)) || (user && user.member_id==bargainorder_info.bargainorder_initiator_id)?'active':''" @click="addLog"><img class="gloss" src="../../../assets/image/home/home-bargain-gloss.png" />{{bargainorder_info.bargain_if_add?(user && user.member_id==bargainorder_info.bargainorder_initiator_id?(bargainorder_info.bargain_total<=bargainorder_info.bargainorder_times?'砍价完成去下单':'邀请好友帮砍'):(bargainorder_info.bargain_total<=bargainorder_info.bargainorder_times?'砍价已完成':'帮好友砍一刀')):'您已经砍过价了'}}</mt-button>
            <div class="notice">加油！成败在此一举</div>
        </div>
        <div class="bargain-log">
            <div class="title mt-5">| 砍价记录 |</div>
            <div class="scroll-wrapper" v-if="bargainlog_list && bargainlog_list.length">
                <ul :class="'line-'+bargainlog_list.length">
                    <li v-for="(item,index) in bargainlog_list">
                        <div class="p_img"><img :src="item.pbargainlog_member_avatar"/></div>
                        <div class="p_name">{{item.pbargainlog_member_name}}</div>
                        <div class="p_price">帮砍<em>￥{{item.pbargainlog_price}}</em></div>
                    </li>
                    <slot v-if="bargainlog_list.length>3">
                    <li v-for="i in 3">
                        <div class="p_img"><img :src="bargainlog_list[i].pbargainlog_member_avatar"/></div>
                        <div class="p_name">{{bargainlog_list[i].pbargainlog_member_name}}</div>
                        <div class="p_price">帮砍<em>￥{{bargainlog_list[i].pbargainlog_price}}</em></div>
                    </li>
                    </slot>
                </ul>

            </div>
            <div v-else class="null">暂无砍价记录</div>
        </div>
    </div>
        <mt-popup v-model="posterVisible">
            <img :src="posterUrl">
        </mt-popup>
    </div>
</template>

<script>
import { Toast, MessageBox } from 'mint-ui'
import { getBargainInfo, getBargainOrderInfo, getBargainLogList } from '../../../api/homeBargain'
import { addBargainLog } from '../../../api/memberBargain'
import { mapState } from 'vuex'
export default {
  name: 'Share',
  data () {
    return {
      flag: false,
      posterVisible: false,
      posterUrl: '',
      hours: '',
      minute: '',
      second: '',
      time: false,
      isshow: true,
      bargainorder_id: this.$route.query.bargainorder_id,
      bargainorder_info: false,
      bargainlog_list: false
    }
  },
  created: function () {
    if (!this.bargainorder_id) {
      Toast('缺少参数')
    } else {
      this.posterUrl = process.env.VUE_APP_SITE_URL + '/home/qrcode?url=' + encodeURIComponent(process.envH5_HOST + '/home/bargain_share?bargainorder_id=' + this.bargainorder_id)
      this.getBargainOrderInfo()
    }
  },
  computed: mapState({
    user: state => state.member.info,
    token: state => state.member.token
  }),
  mounted () {
    this.time = setInterval(() => {
      if (this.flag == false) {
        clearInterval(this.time)
      } else {
        this.timeDown()
      }
    }, 1000)
  },
  methods: {
    goGoods () {
      this.$router.push({ name: 'HomeGoodsdetail', query: { goods_id: this.bargainorder_info.bargain_goods_id, bargain_id: this.bargainorder_info.bargain_id } })
    },
    getBargainOrderInfo () {
      getBargainOrderInfo(this.bargainorder_id, this.token).then(res => {
        this.bargainorder_info = res.result.bargainorder_info
        let nowTime = new Date()
        if (this.bargainorder_info.bargainorder_endtime > nowTime.getTime() / 1000) {
          this.flag = true
        }
        getBargainLogList(this.bargainorder_id, { per_page: 10 }).then(res => {
          this.bargainlog_list = res.result.bargainlog_list
        }).catch(function (error) {
          Toast(error.message)
        })
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    addLog () {
      if (!this.flag || !this.bargainorder_info.bargain_if_add) {
        return
      }
      if (this.bargainorder_info.bargain_total <= this.bargainorder_info.bargainorder_times) {
        if (this.user && this.user.member_id == this.bargainorder_info.bargainorder_initiator_id) {
          getBargainInfo(this.bargainorder_info.bargain_id).then(res => {
            MessageBox.prompt('不可超过' + res.result.bargain_info.bargain_limit, '请填写购买数量', { inputType: 'number' }).then(({ value, action }) => {
              value = parseInt(value)
              if (value < 1 || value > res.result.bargain_info.bargain_limit) {
                Toast('数量填写错误')
                return
              }
              let params = { buy_now: 1, cart_id: this.bargainorder_info.bargain_goods_id + '|' + value, bargainorder_id: this.bargainorder_id }
              this.$router.push({ name: 'MemberBuyStep1', query: params })
            })
          }).catch(function (error) {
            Toast(error.message)
          })
        }
        return
      }
      if (this.user && this.user.member_id == this.bargainorder_info.bargainorder_initiator_id) {
        this.posterVisible = true
      } else {
        addBargainLog(this.bargainorder_id).then(res => {
          this.getBargainOrderInfo()
        }).catch(function (error) {
          Toast(error.message)
        })
      }
    },
    /*
       * timeDown: 倒计时
       */
    timeDown () {
      let end_time = false
      if (this.bargainorder_info) {
        end_time = this.bargainorder_info.bargainorder_endtime
      }
      if (end_time) {
        const endTime = new Date(end_time * 1000)
        const nowTime = new Date()
        let leftTime = parseInt((endTime.getTime() - nowTime.getTime()) / 1000)
        this.hours = this.formate(parseInt((leftTime / (60 * 60)) % 24))
        this.minute = this.formate(parseInt((leftTime / 60) % 60))
        this.second = this.formate(parseInt(leftTime % 60))
        if (leftTime <= 0) {
          this.flag = false
        }
      }
    },
    /*
       * 格式化时间
       */
    formate (time) {
      if (time >= 10) {
        return time
      } else {
        return `0${time}`
      }
    }
  }
}
</script>

<style scoped lang="scss">
    @keyframes scaleDraw {0% {transform: scale(1);}
        25% {transform: scale(1.05);}
        50% {transform: scale(1);}
        75% {transform: scale(1.05);}}
    @keyframes scrollText10 {0% {transform: translateY(0px);}
        10% {transform: translateY(-2.5rem);}
        20% {transform: translateY(-5rem);}
        30% {transform: translateY(-7.5rem);}
        40% {transform: translateY(-10rem);}
        50% {transform: translateY(-12.5rem);}
        60% {transform: translateY(-15rem);}
        70% {transform: translateY(-17.5rem);}
        80% {transform: translateY(-20rem);}
        90% {transform: translateY(-22.5rem);}
        100% {transform: translateY(-25rem);}}
    @keyframes scrollText9 {0% {transform: translateY(0px);}
        11.1111% {transform: translateY(-2.5rem);}
        22.2222% {transform: translateY(-5rem);}
        33.3333% {transform: translateY(-7.5rem);}
        44.4444% {transform: translateY(-10rem);}
        55.5555% {transform: translateY(-12.5rem);}
        66.6666% {transform: translateY(-15rem);}
        77.7777% {transform: translateY(-17.5rem);}
        88.8888% {transform: translateY(-20rem);}
        99.9999% {transform: translateY(-22.5rem);}}
    @keyframes scrollText8 {0% {transform: translateY(0px);}
        12.5% {transform: translateY(-2.5rem);}
        25% {transform: translateY(-5rem);}
        37.5% {transform: translateY(-7.5rem);}
        50% {transform: translateY(-10rem);}
        62.5% {transform: translateY(-12.5rem);}
        75% {transform: translateY(-15rem);}
        87.5% {transform: translateY(-17.5rem);}
        100% {transform: translateY(-20rem);}}
    @keyframes scrollText7 {0% {transform: translateY(0px);}
        14.2857% {transform: translateY(-2.5rem);}
        28.5714% {transform: translateY(-5rem);}
        42.8571% {transform: translateY(-7.5rem);}
        57.1428% {transform: translateY(-10rem);}
        71.4285% {transform: translateY(-12.5rem);}
        85.7142% {transform: translateY(-15rem);}
        99.9999% {transform: translateY(-17.5rem);}}
    @keyframes scrollText6 {0% {transform: translateY(0px);}
        16.6666% {transform: translateY(-2.5rem);}
        33.3332% {transform: translateY(-5rem);}
        49.9998% {transform: translateY(-7.5rem);}
        66.6664% {transform: translateY(-10rem);}
        83.333% {transform: translateY(-12.5rem);}
        99.9996% {transform: translateY(-15rem);}}
    @keyframes scrollText5 {0% {transform: translateY(0px);}
        20% {transform: translateY(-2.5rem);}
        40% {transform: translateY(-5rem);}
        60% {transform: translateY(-7.5rem);}
        80% {transform: translateY(-10rem);}
        100% {transform: translateY(-12.5rem);}}
    @keyframes scrollText4 {0% {transform: translateY(0px);}
        25% {transform: translateY(-2.5rem);}
        50% {transform: translateY(-5rem);}
        75% {transform: translateY(-7.5rem);}
        100% {transform: translateY(-10rem);}}
    .whole-wrapper{position:fixed;width:100%;height: 100%;background-color: #EA3F64;overflow: auto}
    .bargain-share {background-color: #EA3F64;background-image: url(../../../assets/image/home/home-bargain-bg.jpg);background-repeat: no-repeat;background-size: 100%;background-position: top center;position: relative;padding-top: 1rem ;padding-bottom: 3rem;}
    .bargain-share.inactive{color: #a1a1a1}
    .bargain-share.inactive .bargain-info .bargain_remark{color: #a1a1a1}
    .bargain-share.inactive .bargain-info .goods_info .bargain_goods_price{color: #a1a1a1}
    .bargain-share.inactive .expired{color:red;border:2px solid red;padding:.2rem;font-weight: bold;display: inline-block;position: absolute;right:.5rem;bottom:1rem;transform: rotate(-30deg)}
    .bargain-info,.bargain-order,.bargain-log {padding: 1rem;background: #fff;width: 88%;margin: 1rem auto 0;position: relative;border-radius: 0.4rem;font-size: .6rem;box-sizing: border-box}
    .bargain-info .member_avatar {position: absolute;left: 50%;transform: translateX(-50%);top: -1rem;text-align: center;}
    .bargain-info .member_avatar img {width: 2rem;max-width: 100%;max-height: 100%;vertical-align: middle;border-radius: 50%;}
    .bargain-info .member_name {text-align: center;margin-top: 0.6rem;line-height: 1rem;}
    .bargain-info .bargain_remark {text-align: center;line-height: 1.5rem;font-size: 0.7rem;color: #9a6e3a}
    .bargain-info .goods_info {height: 4rem;display: flex;margin-bottom: 1rem;}
    .bargain-info .goods_info .p_img {}
    .bargain-info .goods_info .p_img img {width: 4rem;height: 4rem;}
    .bargain-info .goods_info .p_info {position: relative;height: 4rem}
    .bargain-info .goods_info .goods_name {line-height: 1rem;height: 2rem;overflow: hidden;display: -webkit-box;-webkit-line-clamp: 2;-webkit-box-orient: vertical;line-clamp: 2;box-orient: vertical;}
    .bargain-info .goods_info .bargain_goods_price {line-height: 1rem;color: red;position: absolute;bottom: 0}
    .bargain-info .goods_info .bargain_goods_price em {font-size: .8rem;}
    .bargain-info .goods_info .bargain_goods_price del {color: #a1a1a1;margin-left: .2rem;}
    .bargain-order {text-align: center;padding-top: 1rem;padding-bottom: 1rem;}
    .bargain-order .count {background: #ffeff2;color: #f12c56;padding: 0 1rem;border-radius: 1.5rem;height: 1.5rem;line-height: 1.5rem;box-sizing: border-box;display: inline-block}
    .bargain-order .notice {color: gray;}
    .bargain-log {}
    .bargain-log .title {text-align: center;color: #f12c56}
    .bargain-log .scroll-wrapper {overflow: hidden;height: 7.5rem;}
    .bargain-log .null{padding:1rem;color:gray;text-align: center}
    .bargain-log ul {top: 0;position: relative;}
    .bargain-log ul.line-10{-webkit-animation: scrollText10 20s infinite  cubic-bezier(1,0,0.5,0);animation: scrollText10 20s infinite  cubic-bezier(1,0,0.5,0);}
    .bargain-log ul.line-9{-webkit-animation: scrollText9 18s infinite  cubic-bezier(1,0,0.5,0);animation: scrollText9 18s infinite  cubic-bezier(1,0,0.5,0);}
    .bargain-log ul.line-8{-webkit-animation: scrollText8 16s infinite  cubic-bezier(1,0,0.5,0);animation: scrollText8 16s infinite  cubic-bezier(1,0,0.5,0);}
    .bargain-log ul.line-7{-webkit-animation: scrollText7 14s infinite  cubic-bezier(1,0,0.5,0);animation: scrollText7 14s infinite  cubic-bezier(1,0,0.5,0);}
    .bargain-log ul.line-6{-webkit-animation: scrollText6 12s infinite  cubic-bezier(1,0,0.5,0);animation: scrollText6 12s infinite  cubic-bezier(1,0,0.5,0);}
    .bargain-log ul.line-5{-webkit-animation: scrollText5 10s infinite  cubic-bezier(1,0,0.5,0);animation: scrollText5 10s infinite  cubic-bezier(1,0,0.5,0);}
    .bargain-log ul.line-4{-webkit-animation: scrollText4 8s infinite  cubic-bezier(1,0,0.5,0);animation: scrollText4 8s infinite  cubic-bezier(1,0,0.5,0);}
    .bargain-log li {height: 2rem;line-height: 2rem;width: 100%;overflow: hidden;margin: 0.5rem 0;}
    .bargain-log li .p_img {float: left;}
    .bargain-log li .p_img img {width: 2rem;height: 2rem;border-radius: 50%;}
    .bargain-log li .p_name {float: left;margin-left: 1rem;}
    .bargain-log li .p_price {float: right;}
    .bargain-log li .p_price em {color: red;}
    .bargain-order .connect-wrapper {position: absolute;height: 2.5rem;top: -1.75rem;z-index: 2}
    .bargain-order .connect-wrapper .dot {width: .5rem;height: .5rem;border-radius: 50%;background: #EA3F64}
    .bargain-order .connect-wrapper .bottom-dot {bottom: 0;position: absolute;}
    .bargain-order .connect-wrapper .round {width: .3rem;height: 2rem;background: #FF8C00;border-radius: .3rem;position: absolute;top: .25rem;left: .1rem;}
    .bargain-order .connect-wrapper.right {right: 10px;}
    .bargain-order .button-1 {background:#999 !important;width: 100%;font-size: .65rem;height: 2rem;line-height: 2rem;border-radius: 2rem;box-shadow: 0px 5px 10px 0px #c3c3c3;}
    .bargain-order .button-1 .gloss {display: none;position: absolute;z-index: 1;left: .8rem;top: .2rem;height: .3rem;width: 5rem;opacity: .8;}
    .bargain-order .button-1.active{-webkit-animation: scaleDraw 2s ease-in-out infinite;background: #EA3F64 !important;background-image: linear-gradient(to right, #ff5e00, #ea3f3f) !important;}
    .bargain-order .button-1.active .gloss{display: block}
    .progress-wrapper {position: relative;width: 100%}
    .progress-wrapper .progress-back {background: #ffeff2;width: 100%;height: .3rem;border-radius: .3rem;}
    .progress-wrapper .progress-front {background: #f12c56;width: 0%;height: .3rem;border-radius: .3rem;position: absolute;}
    .progress-wrapper .progress-front .arrow {position: absolute;right: 0;background: #f12c56;text-align: center;height: .8rem;width: .8rem;border-radius: 50%;line-height: .8rem;font-size: .6rem;top: -.25rem;}
    .progress-wrapper .progress-front .arrow:before {width: .3rem;height: .3rem;background: #ffeff2;content: "";display: block;margin: .25rem auto;border-radius: 50%;}
    .count-down-wrapper {color: #f12c56;line-height: 1rem;}
    .count-down-wrapper .block {display: inline-block;width: 1rem;height: 1rem;border-radius: .2rem;color: #fff;line-height: 1rem;background: #f12c56;margin: 0 .2rem;vertical-align: middle}
</style>
