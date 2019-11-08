<template>
    <div class="home-marketzodiac">
        <div class="apps-zodiac">
            <div class="zodiac-wrap">
                <ul class="zodiac">
                    <li class="zodiac-item zodiac-item1" @click="selectZodiac(1)" :class="{'animated tada flaunt':(active==1),'select':(select==1),'prize':(prize==1),'flipped':(flipped)}"> <div v-if="show && show[0]" class="zodiac-item-flip"><div class="zodiac-item-icon zodiac-item-icon-front"></div><div class="zodiac-item-icon zodiac-item-icon-back"></div></div></li>
                    <li class="zodiac-item zodiac-item2" @click="selectZodiac(2)" :class="{'animated tada flaunt':(active==2),'select':(select==2),'prize':(prize==2),'flipped':(flipped)}"> <div v-if="show && show[1]" class="zodiac-item-flip"><div class="zodiac-item-icon zodiac-item-icon-front"></div><div class="zodiac-item-icon zodiac-item-icon-back"></div></div></li>
                    <li class="zodiac-item zodiac-item3" @click="selectZodiac(3)" :class="{'animated tada flaunt':(active==3),'select':(select==3),'prize':(prize==3),'flipped':(flipped)}"> <div v-if="show && show[2]" class="zodiac-item-flip"><div class="zodiac-item-icon zodiac-item-icon-front"></div><div class="zodiac-item-icon zodiac-item-icon-back"></div></div></li>
                    <li class="zodiac-item zodiac-item4" @click="selectZodiac(4)" :class="{'animated tada flaunt':(active==4),'select':(select==4),'prize':(prize==4),'flipped':(flipped)}"> <div v-if="show && show[3]" class="zodiac-item-flip"><div class="zodiac-item-icon zodiac-item-icon-front"></div><div class="zodiac-item-icon zodiac-item-icon-back"></div></div></li>
                    <li class="zodiac-item zodiac-item5" @click="selectZodiac(5)" :class="{'animated tada flaunt':(active==5),'select':(select==5),'prize':(prize==5),'flipped':(flipped)}"> <div v-if="show && show[4]" class="zodiac-item-flip"><div class="zodiac-item-icon zodiac-item-icon-front"></div><div class="zodiac-item-icon zodiac-item-icon-back"></div></div></li>
                    <li class="zodiac-item zodiac-item6" @click="selectZodiac(6)" :class="{'animated tada flaunt':(active==6),'select':(select==6),'prize':(prize==6),'flipped':(flipped)}"> <div v-if="show && show[5]" class="zodiac-item-flip"><div class="zodiac-item-icon zodiac-item-icon-front"></div><div class="zodiac-item-icon zodiac-item-icon-back"></div></div></li>
                    <li class="zodiac-item zodiac-item7" @click="selectZodiac(7)" :class="{'animated tada flaunt':(active==7),'select':(select==7),'prize':(prize==7),'flipped':(flipped)}"> <div v-if="show && show[6]" class="zodiac-item-flip"><div class="zodiac-item-icon zodiac-item-icon-front"></div><div class="zodiac-item-icon zodiac-item-icon-back"></div></div></li>
                    <li class="zodiac-item zodiac-item8" @click="selectZodiac(8)" :class="{'animated tada flaunt':(active==8),'select':(select==8),'prize':(prize==8),'flipped':(flipped)}"> <div v-if="show && show[7]" class="zodiac-item-flip"><div class="zodiac-item-icon zodiac-item-icon-front"></div><div class="zodiac-item-icon zodiac-item-icon-back"></div></div></li>
                    <li class="zodiac-item zodiac-item9" @click="selectZodiac(9)" :class="{'animated tada flaunt':(active==9),'select':(select==9),'prize':(prize==9),'flipped':(flipped)}"> <div v-if="show && show[8]" class="zodiac-item-flip"><div class="zodiac-item-icon zodiac-item-icon-front"></div><div class="zodiac-item-icon zodiac-item-icon-back"></div></div></li>
                    <li class="zodiac-item zodiac-item10" @click="selectZodiac(10)" :class="{'animated tada flaunt':(active==10),'select':(select==10),'prize':(prize==10),'flipped':(flipped)}"> <div v-if="show && show[9]" class="zodiac-item-flip"><div class="zodiac-item-icon zodiac-item-icon-front"></div><div class="zodiac-item-icon zodiac-item-icon-back"></div></div></li>
                    <li class="zodiac-item zodiac-item11" @click="selectZodiac(11)" :class="{'animated tada flaunt':(active==11),'select':(select==11),'prize':(prize==11),'flipped':(flipped)}"> <div v-if="show && show[10]" class="zodiac-item-flip"><div class="zodiac-item-icon zodiac-item-icon-front"></div><div class="zodiac-item-icon zodiac-item-icon-back"></div></div></li>
                    <li class="zodiac-item zodiac-item12" @click="selectZodiac(12)" :class="{'animated tada flaunt':(active==12),'select':(select==12),'prize':(prize==12),'flipped':(flipped)}"> <div v-if="show && show[11]" class="zodiac-item-flip"><div class="zodiac-item-icon zodiac-item-icon-front"></div><div class="zodiac-item-icon zodiac-item-icon-back"></div></div></li>
                </ul>
            </div>

            <div class="info-block">
                <div class="info-area">
                    <div class="view-prize"><a class="js-view-prize" @click="reset" v-if="canDraw">再来一次</a><a class="js-view-prize" @click="goLog">查看奖品</a></div>
                    <ul class="activity-info" v-if="marketmanage_info">
                        <li>活动名称：
                            <div class="activity-info-content">{{marketmanage_info.marketmanage_name}}</div>
                        </li>
                        <li>活动介绍：
                            <div class="activity-info-content">{{marketmanage_info.marketmanage_detail}}</div>
                        </li>
                        <li>活动有效时间：
                            <div class="activity-info-content">{{marketmanage_info.marketmanage_begintime_text}}至{{marketmanage_info.marketmanage_endtime_text}}</div>
                        </li>
                        <li>活动奖项设置：
                            <div class="activity-info-content">
                                <div v-for="(marketmanageaward,index) in marketmanageaward_list" :key="index">{{marketmanageaward.marketmanageaward_level}}等奖{{marketmanageaward.marketmanageaward_probability}}%：{{marketmanageaward.marketmanageaward_text}}</div>
                            </div>
                        </li>

                    </ul>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
import { getMarketmanageInfo, addMarketmanagelog } from '../../../api/homeMarketmanage'
import { Toast, MessageBox } from 'mint-ui'
import { mapState } from 'vuex'
export default {
  data () {
    return {
      show: false,
      active: false,
      select: false,
      prize: false,
      flipped: false,
      marketmanage_id: 0,
      marketmanage_info: false,
      marketmanageaward_list: false,
      isScrape: false,
      isResult: false,
      drawResult: false,
      drawTitle: '真遗憾，未中奖',
      drawContent: '哎呀，肯定姿势不对！',
      canDraw: false,
      time_interval: false
    }
  },
  computed: {
    ...mapState({
      isOnline: state => state.member.isOnline,
      token: state => state.member.token
    })
  },
  created: function () {
    this.marketmanage_id = this.$route.query.marketmanage_id
    if (!this.marketmanage_id) {
      Toast('参数错误')
      return
    }
    getMarketmanageInfo(this.marketmanage_id, this.token).then(res => {
      this.marketmanage_info = res.result.marketmanage_info
      this.marketmanageaward_list = res.result.marketmanageaward_list
      this.canDraw = res.result.can_draw
      this.drawContent = this.marketmanage_info.marketmanage_failed
      if (!this.canDraw) {
        Toast(res.message)
      }
    }).catch(function (error) {
      Toast(error.message)
    })
  },
  methods: {
    goLog () {
      this.$router.push({ name: 'MemberMarketmanagelog' })
    },
    setActive () {
      let _this = this
      this.time_interval = setInterval(function () {
        _this.active = parseInt(Math.random() * 12) % 12 + 1
      }, 3000)
    },
    selectZodiac (index) {
      if (!this.isOnline) {
        this.$router.push({ name: 'HomeMemberLogin' })
        return
      }
      if (!this.canDraw) return
      if (!this.isScrape) { // 消耗刮卡机会
        this.isScrape = true
        addMarketmanagelog(this.marketmanage_id).then(res => {
          this.canDraw = res.result.can_draw
          this.isResult = true
          this.select = index
          this.flipped = true
          clearInterval(this.time_interval)
          if (res.result.draw_result) { // 中奖
            this.prize = index
            this.drawResult = true
            this.drawTitle = '恭喜你，获得' + res.result.draw_info.marketmanageaward_level + '等奖'
            this.drawContent = res.result.draw_info.marketmanageaward_text
          } else {
            this.prize = (index + parseInt(Math.random() * 11)) % 12 + 1
          }
          MessageBox.alert(this.drawContent, this.drawTitle)
        }).catch(function (error) {
          Toast(error.message)
        })
      }
    },
    reset () {
      // 重置
      this.select = false
      this.prize = false
      this.isScrape = false
      this.flipped = false
      this.drawResult = false
      this.isResult = false
      this.drawTitle = '真遗憾，未中奖'
      this.drawContent = this.marketmanage_info.marketmanage_failed
      this.setActive()
    }
  },
  watch: {

  },
  beforeDestroy: function () {
    clearInterval(this.time_interval)
  },
  mounted () {
    this.show = []
    let _this = this
    this.time_interval = setInterval(function () {
      if (_this.show.length < 12) {
        _this.show.push(true)
      } else {
        clearInterval(_this.time_interval)
        _this.setActive()
      }
    }, 100)
  }
}
</script>

<style scoped lang="scss">
    .tada {
        -webkit-animation-name: tada;
        -moz-animation-name: tada;
        animation-name: tada;
    }
    .animated {
        -webkit-animation-duration: 1s;
        -moz-animation-duration: 1s;
        animation-duration: 1s;
        -webkit-animation-fill-mode: both;
        -moz-animation-fill-mode: both;
        animation-fill-mode: both;
    }

    .animated.infinite {
        -webkit-animation-iteration-count: infinite;
        -moz-animation-iteration-count: infinite;
        animation-iteration-count: infinite
    }

    @-webkit-keyframes tada {
        0% {
            -webkit-transform: scale(1)
        }

        10%,20% {
            -webkit-transform: scale(0.9) rotate(-3deg)
        }

        30%,50%,70%,90% {
            -webkit-transform: scale(1.1) rotate(3deg)
        }

        40%,60%,80% {
            -webkit-transform: scale(1.1) rotate(-3deg)
        }

        100% {
            -webkit-transform: scale(1) rotate(0)
        }
    }

    @-moz-keyframes tada {
        0% {
            -moz-transform: scale(1)
        }

        10%,20% {
            -moz-transform: scale(0.9) rotate(-3deg)
        }

        30%,50%,70%,90% {
            -moz-transform: scale(1.1) rotate(3deg)
        }

        40%,60%,80% {
            -moz-transform: scale(1.1) rotate(-3deg)
        }

        100% {
            -moz-transform: scale(1) rotate(0)
        }
    }

    @keyframes tada {
        0% {
            -webkit-transform: scale(1);
            -moz-transform: scale(1);
            transform: scale(1)
        }

        10%,20% {
            -webkit-transform: scale(0.9) rotate(-3deg);
            -moz-transform: scale(0.9) rotate(-3deg);
            transform: scale(0.9) rotate(-3deg)
        }

        30%,50%,70%,90% {
            -webkit-transform: scale(1.1) rotate(3deg);
            -moz-transform: scale(1.1) rotate(3deg);
            transform: scale(1.1) rotate(3deg)
        }

        40%,60%,80% {
            -webkit-transform: scale(1.1) rotate(-3deg);
            -moz-transform: scale(1.1) rotate(-3deg);
            transform: scale(1.1) rotate(-3deg)
        }

        100% {
            -webkit-transform: scale(1) rotate(0);
            -moz-transform: scale(1) rotate(0);
            transform: scale(1) rotate(0)
        }
    }

    .apps-zodiac {
        position: fixed;width:100%;height:100%;background: #77a9af;overflow-y:auto;
    }
    .apps-zodiac .zodiac-wrap {
        position: relative;
        width: 280px;
        padding: 10px 0 40px;
        margin: 0 auto
    }

    .apps-zodiac .zodiac-wrap * {
        -webkit-user-select: none;
        -moz-user-select: none;
        -ms-user-select: none;
        user-select: none
    }

    .apps-zodiac .zodiac .zodiac-item {
        display: inline-block;
        -webkit-perspective: 800;
        -moz-perspective: 800;
        perspective: 800;
        width: 74px;
        height: 74px;
        position: relative;
        margin: 10px 8px
    }

    .apps-zodiac .zodiac .zodiac-item .zodiac-item-flip {
        width: 100%;
        height: 100%;
        -webkit-transform-style: preserve-3d;
        -moz-transform-style: preserve-3d;
        transform-style: preserve-3d;
        -webkit-transition: 0.5s;
        -moz-transition: 0.5s;
        transition: 0.5s
    }

    .apps-zodiac .zodiac .zodiac-item .zodiac-item-flip .zodiac-item-icon {
        position: absolute;
        border-radius: 50%;
        width: 66px;
        height: 66px;
        border: 4px solid white;
        -webkit-box-shadow: 0 1px 2px 1px rgba(0,0,0,0.2),inset 0 0 5px rgba(0,0,0,0.1);
        box-shadow: 0 1px 2px 1px rgba(0,0,0,0.2),inset 0 0 5px rgba(0,0,0,0.1);
        -webkit-backface-visibility: hidden;
        -moz-backface-visibility: hidden;
        backface-visibility: hidden;
        text-align: center;
        line-height: 66px;
        cursor: pointer
    }

    .apps-zodiac .zodiac .zodiac-item .zodiac-item-flip .zodiac-item-icon-front {
        position: absolute;
        z-index: 1
    }

    .apps-zodiac .zodiac .zodiac-item .zodiac-item-flip .zodiac-item-icon-back {
        -webkit-transform: rotateY(-180deg);
        -moz-transform: rotateY(-180deg);
        transform: rotateY(-180deg);
        font-size: 35px;
        color: #ff0
    }

    .apps-zodiac .zodiac .zodiac-item.flaunt .zodiac-item-icon-front::after {
        content: '点我';
        position: absolute;
        top: 10px;
        right: -20px;
        width: 30px;
        height: 20px;
        z-index: 20;
        text-align: center;
        line-height: 20px;
        font-size: 12px;
        background: #444;
        color: #fff
    }

    .apps-zodiac .zodiac .zodiac-item.flaunt .zodiac-item-icon-front::before {
        content: '';
        position: absolute;
        right: 1px;
        top: 22px;
        z-index: 20;
        border: 8px solid transparent;
        border-top-color: #444;
        -webkit-transform: rotate(-45deg);
        -moz-transform: rotate(-45deg);
        -ms-transform: rotate(-45deg);
        transform: rotate(-45deg)
    }

    .apps-zodiac .zodiac .zodiac-item.wave .zodiac-item-flip {
        -webkit-animation: 2s flip reverse infinite linear;
        -moz-animation: 2s flip reverse infinite linear;
        animation: 2s flip reverse infinite linear
    }

    .apps-zodiac .zodiac .zodiac-item.flipped .zodiac-item-flip {
        -webkit-transform: rotateY(-180deg);
        -moz-transform: rotateY(-180deg);
        transform: rotateY(-180deg)
    }

    .apps-zodiac .zodiac .zodiac-item.prize .zodiac-item-icon-back::before {
        content: '奖'
    }

    .apps-zodiac .zodiac .zodiac-item.select .zodiac-item-icon {
        border-color: #ff0
    }

    .apps-zodiac .zodiac .zodiac-item-icon-front {
        background-size: cover
    }

    .apps-zodiac .zodiac .zodiac-item1 .zodiac-item-icon {
        background-color: #30a8c0
    }

    .apps-zodiac .zodiac .zodiac-item1 .zodiac-item-icon.zodiac-item-icon-front {
        background-image: url("../../../assets/image/home/home-marketzodiac-1.png")
    }

    .apps-zodiac .zodiac .zodiac-item2 .zodiac-item-icon {
        background-color: #5f95d5
    }

    .apps-zodiac .zodiac .zodiac-item2 .zodiac-item-icon.zodiac-item-icon-front {
        background-image: url("../../../assets/image/home/home-marketzodiac-2.png")
    }

    .apps-zodiac .zodiac .zodiac-item3 .zodiac-item-icon {
        background-color: #9179b5
    }

    .apps-zodiac .zodiac .zodiac-item3 .zodiac-item-icon.zodiac-item-icon-front {
        background-image: url("../../../assets/image/home/home-marketzodiac-3.png")
    }

    .apps-zodiac .zodiac .zodiac-item4 .zodiac-item-icon {
        background-color: #2bac6b
    }

    .apps-zodiac .zodiac .zodiac-item4 .zodiac-item-icon.zodiac-item-icon-front {
        background-image: url("../../../assets/image/home/home-marketzodiac-4.png")
    }

    .apps-zodiac .zodiac .zodiac-item5 .zodiac-item-icon {
        background-color: #ee7c86
    }

    .apps-zodiac .zodiac .zodiac-item5 .zodiac-item-icon.zodiac-item-icon-front {
        background-image: url("../../../assets/image/home/home-marketzodiac-5.png")
    }

    .apps-zodiac .zodiac .zodiac-item6 .zodiac-item-icon {
        background-color: #cae0a0
    }

    .apps-zodiac .zodiac .zodiac-item6 .zodiac-item-icon.zodiac-item-icon-front {
        background-image: url("../../../assets/image/home/home-marketzodiac-6.png")
    }

    .apps-zodiac .zodiac .zodiac-item7 .zodiac-item-icon {
        background-color: #deb94f
    }

    .apps-zodiac .zodiac .zodiac-item7 .zodiac-item-icon.zodiac-item-icon-front {
        background-image: url("../../../assets/image/home/home-marketzodiac-7.png")
    }

    .apps-zodiac .zodiac .zodiac-item8 .zodiac-item-icon {
        background-color: #a38f48
    }

    .apps-zodiac .zodiac .zodiac-item8 .zodiac-item-icon.zodiac-item-icon-front {
        background-image: url("../../../assets/image/home/home-marketzodiac-8.png")
    }

    .apps-zodiac .zodiac .zodiac-item9 .zodiac-item-icon {
        background-color: #8cbe43
    }

    .apps-zodiac .zodiac .zodiac-item9 .zodiac-item-icon.zodiac-item-icon-front {
        background-image: url("../../../assets/image/home/home-marketzodiac-9.png")
    }

    .apps-zodiac .zodiac .zodiac-item10 .zodiac-item-icon {
        background-color: #6a7dcc
    }

    .apps-zodiac .zodiac .zodiac-item10 .zodiac-item-icon.zodiac-item-icon-front {
        background-image: url("../../../assets/image/home/home-marketzodiac-10.png")
    }

    .apps-zodiac .zodiac .zodiac-item11 .zodiac-item-icon {
        background-color: #84c8a1
    }

    .apps-zodiac .zodiac .zodiac-item11 .zodiac-item-icon.zodiac-item-icon-front {
        background-image: url("../../../assets/image/home/home-marketzodiac-11.png")
    }

    .apps-zodiac .zodiac .zodiac-item12 .zodiac-item-icon {
        background-color: #599ca2
    }

    .apps-zodiac .zodiac .zodiac-item12 .zodiac-item-icon.zodiac-item-icon-front {
        background-image: url("../../../assets/image/home/home-marketzodiac-12.png")
    }

    .apps-zodiac .info-block {
        background-color: #649ea5
    }

    .apps-zodiac ul.activity-info {
        margin-top: -10px;
        color: #fff;
        padding-top:1rem;
    }
    .apps-zodiac ul.activity-info li{padding-bottom: 1rem;}
    .apps-zodiac ul.activity-info a.user-center {
        color: #fee25f
    }

    .apps-zodiac .view-prize {
        position: relative;
        top: -35px;
        padding-right: 15px;
        padding-left: 15px;
        overflow: hidden;
    }

    .apps-zodiac .view-prize a,.apps-zodiac .view-prize a:hover {
        color: #fee25f
    }
    .apps-zodiac .view-prize a:last-child{float: right}
    @keyframes flip {
        0% {
            -webkit-transform: rotate(0) rotateY(0);
            -moz-transform: rotate(0) rotateY(0);
            transform: rotate(0) rotateY(0)
        }

        8.3% {
            -webkit-transform: rotate(30deg) rotateY(30deg);
            -moz-transform: rotate(30deg) rotateY(30deg);
            transform: rotate(30deg) rotateY(30deg)
        }

        16.6% {
            -webkit-transform: rotate(60deg) rotateY(60deg);
            -moz-transform: rotate(60deg) rotateY(60deg);
            transform: rotate(60deg) rotateY(60deg)
        }

        24.9% {
            -webkit-transform: rotate(90deg) rotateY(30deg);
            -moz-transform: rotate(90deg) rotateY(30deg);
            transform: rotate(90deg) rotateY(30deg)
        }

        33.2% {
            -webkit-transform: rotate(120deg) rotateY(0);
            -moz-transform: rotate(120deg) rotateY(0);
            transform: rotate(120deg) rotateY(0)
        }

        41.5% {
            -webkit-transform: rotate(150deg) rotateY(-30deg);
            -moz-transform: rotate(150deg) rotateY(-30deg);
            transform: rotate(150deg) rotateY(-30deg)
        }

        49.8% {
            -webkit-transform: rotate(180deg) rotateY(-60);
            -moz-transform: rotate(180deg) rotateY(-60);
            transform: rotate(180deg) rotateY(-60)
        }

        58.1% {
            -webkit-transform: rotate(210deg) rotateY(-30deg);
            -moz-transform: rotate(210deg) rotateY(-30deg);
            transform: rotate(210deg) rotateY(-30deg)
        }

        64.4% {
            -webkit-transform: rotate(240deg) rotateY(0);
            -moz-transform: rotate(240deg) rotateY(0);
            transform: rotate(240deg) rotateY(0)
        }

        72.7% {
            -webkit-transform: rotate(270deg) rotateY(30deg);
            -moz-transform: rotate(270deg) rotateY(30deg);
            transform: rotate(270deg) rotateY(30deg)
        }

        83% {
            -webkit-transform: rotate(300deg) rotateY(60deg);
            -moz-transform: rotate(300deg) rotateY(60deg);
            transform: rotate(300deg) rotateY(60deg)
        }

        91.3% {
            -webkit-transform: rotate(330deg) rotateY(30deg);
            -moz-transform: rotate(330deg) rotateY(30deg);
            transform: rotate(330deg) rotateY(30deg)
        }

        100% {
            -webkit-transform: rotate(360deg) rotateY(0);
            -moz-transform: rotate(360deg) rotateY(0);
            transform: rotate(360deg) rotateY(0)
        }
    }

    @-webkit-keyframes flip {
        0% {
            -webkit-transform: rotate(0) rotateY(0);
            transform: rotate(0) rotateY(0)
        }

        8.3% {
            -webkit-transform: rotate(30deg) rotateY(30deg);
            transform: rotate(30deg) rotateY(30deg)
        }

        16.6% {
            -webkit-transform: rotate(60deg) rotateY(60deg);
            transform: rotate(60deg) rotateY(60deg)
        }

        24.9% {
            -webkit-transform: rotate(90deg) rotateY(30deg);
            transform: rotate(90deg) rotateY(30deg)
        }

        33.2% {
            -webkit-transform: rotate(120deg) rotateY(0);
            transform: rotate(120deg) rotateY(0)
        }

        41.5% {
            -webkit-transform: rotate(150deg) rotateY(-30deg);
            transform: rotate(150deg) rotateY(-30deg)
        }

        49.8% {
            -webkit-transform: rotate(180deg) rotateY(-60);
            transform: rotate(180deg) rotateY(-60)
        }

        58.1% {
            -webkit-transform: rotate(210deg) rotateY(-30deg);
            transform: rotate(210deg) rotateY(-30deg)
        }

        64.4% {
            -webkit-transform: rotate(240deg) rotateY(0);
            transform: rotate(240deg) rotateY(0)
        }

        72.7% {
            -webkit-transform: rotate(270deg) rotateY(30deg);
            transform: rotate(270deg) rotateY(30deg)
        }

        83% {
            -webkit-transform: rotate(300deg) rotateY(60deg);
            transform: rotate(300deg) rotateY(60deg)
        }

        91.3% {
            -webkit-transform: rotate(330deg) rotateY(30deg);
            transform: rotate(330deg) rotateY(30deg)
        }

        100% {
            -webkit-transform: rotate(360deg) rotateY(0);
            transform: rotate(360deg) rotateY(0)
        }
    }

    @-moz-keyframes flip {
        0% {
            -moz-transform: rotate(0) rotateY(0);
            transform: rotate(0) rotateY(0)
        }

        8.3% {
            -moz-transform: rotate(30deg) rotateY(30deg);
            transform: rotate(30deg) rotateY(30deg)
        }

        16.6% {
            -moz-transform: rotate(60deg) rotateY(60deg);
            transform: rotate(60deg) rotateY(60deg)
        }

        24.9% {
            -moz-transform: rotate(90deg) rotateY(30deg);
            transform: rotate(90deg) rotateY(30deg)
        }

        33.2% {
            -moz-transform: rotate(120deg) rotateY(0);
            transform: rotate(120deg) rotateY(0)
        }

        41.5% {
            -moz-transform: rotate(150deg) rotateY(-30deg);
            transform: rotate(150deg) rotateY(-30deg)
        }

        49.8% {
            -moz-transform: rotate(180deg) rotateY(-60);
            transform: rotate(180deg) rotateY(-60)
        }

        58.1% {
            -moz-transform: rotate(210deg) rotateY(-30deg);
            transform: rotate(210deg) rotateY(-30deg)
        }

        64.4% {
            -moz-transform: rotate(240deg) rotateY(0);
            transform: rotate(240deg) rotateY(0)
        }

        72.7% {
            -moz-transform: rotate(270deg) rotateY(30deg);
            transform: rotate(270deg) rotateY(30deg)
        }

        83% {
            -moz-transform: rotate(300deg) rotateY(60deg);
            transform: rotate(300deg) rotateY(60deg)
        }

        91.3% {
            -moz-transform: rotate(330deg) rotateY(30deg);
            transform: rotate(330deg) rotateY(30deg)
        }

        100% {
            -moz-transform: rotate(360deg) rotateY(0);
            transform: rotate(360deg) rotateY(0)
        }
    }
    .info-block{font-size:.6rem;padding:0 1rem;}

</style>
