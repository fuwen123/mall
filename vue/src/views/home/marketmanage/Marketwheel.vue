<template>
    <div class="home-marketwheel">
        <div class="roulette-container l-scene__roulette" v-if="award_list.length">
            <div class="c-award__container" :class="{'c-award__container--active':(active==1)}">
                <i class="c-award__icon" :class="'award-type-'+award_list[1].marketmanageaward_type" />
                <p class="l-general__desc c-award__desc"> {{award_list[1].marketmanageaward_text}} </p>
            </div>
            <div class="c-award__container" :class="{'c-award__container--active':(active==2)}">
                <i class="c-award__icon award-type-0" />
                <p class="l-general__desc c-award__desc"> 谢谢参与 </p>
            </div>
            <div class="c-award__container" :class="{'c-award__container--active':(active==3)}">
                <i class="c-award__icon" :class="'award-type-'+award_list[3].marketmanageaward_type" />
                <p class="l-general__desc c-award__desc"> {{award_list[3].marketmanageaward_text}} </p>
            </div>
            <div class="c-award__container" :class="{'c-award__container--active':(active==4)}">
                <i class="c-award__icon award-type-0" />
                <p class="l-general__desc c-award__desc"> 谢谢参与 </p>
            </div>
            <div class="c-award__container" :class="{'c-award__container--active':(active==12)}">
                <i class="c-award__icon award-type-0" />
                <p class="l-general__desc c-award__desc"> 谢谢参与 </p>
            </div>
            <div class="c-award__container c-award__container--hidden"></div>
            <div class="c-award__container c-award__container--hidden"></div>
            <div class="c-award__container" :class="{'c-award__container--active':(active==5)}">
                <i class="c-award__icon" :class="'award-type-'+award_list[5].marketmanageaward_type" />
                <p class="l-general__desc c-award__desc"> {{award_list[5].marketmanageaward_text}} </p>
            </div>
            <div class="c-award__container" :class="{'c-award__container--active':(active==11)}">
                <i class="c-award__icon" :class="'award-type-'+award_list[11].marketmanageaward_type" />
                <p class="l-general__desc c-award__desc"> {{award_list[11].marketmanageaward_text}} </p>
            </div>
            <div class="c-award__container c-award__container--hidden"></div>
            <div class="c-award__container c-award__container--hidden"></div>
            <div class="c-award__container" :class="{'c-award__container--active':(active==6)}">
                <i class="c-award__icon award-type-0" />
                <p class="l-general__desc c-award__desc"> 谢谢参与 </p>
            </div>
            <div class="c-award__container" :class="{'c-award__container--active':(active==10)}">
                <i class="c-award__icon award-type-0" />
                <p class="l-general__desc c-award__desc"> 谢谢参与 </p>
            </div>
            <div class="c-award__container" :class="{'c-award__container--active':(active==9)}">
                <i class="c-award__icon" :class="'award-type-'+award_list[9].marketmanageaward_type" />
                <p class="l-general__desc c-award__desc"> {{award_list[9].marketmanageaward_text}} </p>
            </div>
            <div class="c-award__container" :class="{'c-award__container--active':(active==8)}">
                <i class="c-award__icon award-type-0" />
                <p class="l-general__desc c-award__desc"> 谢谢参与 </p>
            </div>
            <div class="c-award__container" :class="{'c-award__container--active':(active==7)}">
                <i class="c-award__icon" :class="'award-type-'+award_list[7].marketmanageaward_type" />
                <p class="l-general__desc c-award__desc"> {{award_list[7].marketmanageaward_text}} </p>
            </div>
            <button class="l-lottery__btn" @click="goTurn" v-if="canDraw">
                <div class="c-btn__action">
                    立即抽奖
                </div>
                <div class="c-btn__credit"></div>
                <div class="c-btn__chance" v-if="countLeft">
                    剩余{{countLeft}}次机会
                </div>
            </button>
            <button class="l-lottery__btn l-lottery__btn--disable" v-else>
                <div class="c-btn__action">
                    明天再来
                </div>
                <div class="c-btn__credit"></div>
                <div class="c-btn__chance">
                    次数用完了
                </div>
            </button>
        </div>
        <div class="l-scene__user">
            <p class="c-user__credit">我的积分：{{info?info.member_points:0}}</p>
            <a class="c-user__record" @click="goLog"> 我的中奖记录 </a>
        </div>
        <mt-popup class="popup" v-model="isResult">
            <div class="c-result__popup">
                <div class="c-result__container">
                    <div class="c-result__title">
                        {{drawTitle}}
                    </div>
                    <div class="l-result__prize">
                        <i class="c-prize__icon" :class="'award-type-'+(drawResult?drawInfo.marketmanageaward_type:0)" />
                        <p class="c-prize__name">{{drawContent}}</p>
                        <p class="c-prize__expire"> </p>
                    </div>
                    <div class="button-wrapper">
                        <mt-button type="primary" size="large" @click="isResult=false">
                            知道了
                        </mt-button>
                    </div>
                </div>
            </div>
        </mt-popup>
    </div>
</template>

<script>
import { getMarketmanageInfo, addMarketmanagelog } from '../../../api/homeMarketmanage'
import { getMemberInfo } from '../../../api/member'
import { Toast } from 'mint-ui'
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {
      active: false,
      marketmanage_id: 0,
      marketmanage_info: false,
      award_list: [],
      isResult: false,
      drawResult: false,
      drawTitle: '谢谢参与',
      drawContent: '哎呀，肯定姿势不对！',
      canDraw: false,
      countLeft: 0,
      count: 0,
      award_position: {},
      drawInfo: false
    }
  },
  computed: {
    ...mapState({
      isOnline: state => state.member.isOnline,
      token: state => state.member.token,
      info: state => state.member.info
    })
  },
  created: function () {
    this.marketmanage_id = this.$route.query.marketmanage_id
    if (!this.marketmanage_id) {
      Toast('参数错误')
      return
    }
    if (this.isOnline) {
      this.getMemberInfo()
    }
    getMarketmanageInfo(this.marketmanage_id, this.token).then(res => {
      this.marketmanage_info = res.result.marketmanage_info
      let marketmanageaward_list = res.result.marketmanageaward_list
      let award_list = []
      for (var i = 1; i <= 12; i++) {
        if (i % 2 > 0) {
          award_list[i] = marketmanageaward_list[((i - 1) / 2) % marketmanageaward_list.length]
          if (typeof (this.award_position[award_list[i].marketmanageaward_id]) === 'undefined') {
            this.award_position[award_list[i].marketmanageaward_id] = []
          }
          this.award_position[award_list[i].marketmanageaward_id].push(i)
        } else {
          award_list[i] = false
          if (typeof (this.award_position[0]) === 'undefined') {
            this.award_position[0] = []
          }
          this.award_position[0].push(i)
        }
      }
      this.award_list = award_list
      this.canDraw = res.result.can_draw
      this.countLeft = res.result.count_left
      this.drawContent = this.marketmanage_info.marketmanage_failed
      if (!this.canDraw) {
        Toast(res.message)
      }
    }).catch(function (error) {
      Toast(error.message)
    })
  },
  methods: {
    ...mapMutations({
      memberUpdate: 'memberUpdate'
    }),
    goLog () {
      this.$router.push({ name: 'MemberMarketmanagelog' })
    },
    setActive (speed = 400) {
      if (this.count > 0) {
        if (!this.active) {
          this.active = 1
        } else {
          this.active = this.active % 12 + 1
        }
        this.count = this.count - 1
        let _this = this
        if (speed > 50) {
          speed = speed - 50
        }
        if (this.count < 8) {
          speed = 400 - 50 * this.count
        }
        setTimeout(function () {
          _this.setActive(speed)
        }, speed)
      } else {
        this.isResult = true
      }
    },
    goTurn () {
      if (!this.isOnline) {
        this.$router.push({ name: 'HomeMemberLogin' })
        return
      }
      if (!this.canDraw) return

      // 重置
      this.active = false
      this.drawResult = false
      this.drawTitle = '谢谢参与'
      this.drawContent = this.marketmanage_info.marketmanage_failed
      addMarketmanagelog(this.marketmanage_id).then(res => {
        this.canDraw = res.result.can_draw
        this.countLeft = res.result.count_left
        if (this.marketmanage_info.marketmanage_point > 0) {
          this.getMemberInfo()
        }
        if (res.result.draw_result) { // 中奖
          this.drawResult = true
          this.drawInfo = res.result.draw_info
          this.drawTitle = res.result.draw_info.marketmanageaward_level + '等奖'
          this.drawContent = res.result.draw_info.marketmanageaward_text
          let award_id = res.result.draw_info.marketmanageaward_id
          this.count = 48 + this.award_position[award_id][parseInt(Math.random() * this.award_position[award_id].length) % this.award_position[award_id].length]
        } else {
          this.count = 48 + parseInt(Math.random() * 6) * 2
        }

        this.setActive()
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    getMemberInfo () {
      getMemberInfo().then(res => {
        this.memberUpdate({ info: res.result.member_info })
      }, error => {}
      )
    }
  },
  watch: {

  },

  mounted () {

  }
}
</script>

<style scoped lang="scss">
.home-marketwheel{position: fixed;width:100%;height:100%;background-color: #FE302F;overflow-y:auto;background-image:url(../../../assets/image/home/home-marketwheel-bg.png);background-size:100%;background-repeat: no-repeat;background-position: top center}
.roulette-container {
    display: -webkit-box;
    display: -webkit-flex;
    display: -moz-box;
    display: flex;
    -webkit-flex-wrap: wrap;
    flex-wrap: wrap;
    -webkit-user-select: none;
    -moz-user-select: none;
    user-select: none;
    position: relative;
    -moz-box-sizing: border-box;
    box-sizing: border-box;
    padding: 10px 10px 0;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -moz-box-pack: justify;
    justify-content: space-between;
    margin: 185px 15px 10px 15px;
    background: #fff;
    border-radius: 8px;
    box-shadow: 0 2px 10px 0 rgba(0,0,0,.1);
}
.c-award__container {
    background: #fff;
    border-radius: 4px;
    padding: 10px 0;
    margin-bottom: 10px;
    width: -webkit-calc(25% - 5.5px);
    width: -moz-calc(25% - 5.5px);
    width: calc(25% - 5.5px);
    box-shadow: inset 0 0 24px 0 #ffd6ab,0 .25em 0 rgba(248,166,131,.96)
}

.c-award__container--hidden {
    visibility: hidden
}

.c-award__container--active {
    border-radius: 4px;
    background: #ffceab;
    box-shadow: inset 0 0 8px 0 #ff9358,0 .25em 0 rgba(248,166,131,.96)
}
.c-award__icon {
    width: 32px;
    height: 32px;
    display: block;
    margin: 0 auto;
    background-size:100%;
    background-repeat:no-repeat;
    background-position: center;
}
.c-award__icon.award-type-0{background-image:url(../../../assets/image/home/home-marketwheel-0.png)}
.c-award__icon.award-type-1{background-image:url(../../../assets/image/home/home-marketwheel-1.png)}
.c-award__icon.award-type-2{background-image:url(../../../assets/image/home/home-marketwheel-2.png)}
.c-award__icon.award-type-3{background-image:url(../../../assets/image/home/home-marketwheel-3.png)}
.c-award__desc {
    opacity: .8;
    color: #333;
    margin-top: 5px;
    font-size: 10px;
    line-height: 17px;
    text-align: center
}
.l-lottery__btn {
    top: 50%;
    left: 50%;
    border: none;
    display: -webkit-box;
    display: -webkit-flex;
    display: -moz-box;
    display: flex;
    width: 140px;
    height: 140px;
    color: #fff;
    position: absolute;
    border-radius: 50%;
    -webkit-box-align: center;
    -webkit-align-items: center;
    -moz-box-align: center;
    align-items: center;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -webkit-flex-direction: column;
    -moz-box-orient: vertical;
    -moz-box-direction: normal;
    flex-direction: column;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -moz-box-pack: center;
    justify-content: center;
    -webkit-transform: translate(-50%,-50%);
    -moz-transform: translate(-50%,-50%);
    transform: translate(-50%,-50%);
    background-color: #f44;
    box-shadow: inset 0 0 0 1px rgba(0,0,0,.2),inset 0 -.25em 0 rgba(0,0,0,.25),0 .25em .25em rgba(0,0,0,.05);
    text-decoration: none;
    text-shadow: 0 1px 1px hsla(0,0%,100%,.25);
    vertical-align: middle
}

.l-lottery__btn:active {
    box-shadow: inset 12px 12em 0 hsla(0,0%,100%,.1),inset 0 .25em .5em rgba(0,0,0,.05);
    margin-top: .2em;
    outline: none;
    padding-bottom: .3em
}

.l-lottery__btn--disable {
    box-shadow: 0 .25em 0 rgba(174,165,161,.96);
    background: #d0d0d0
}

.l-lottery__btn--disable:active {
    margin-top: 0;
    padding-bottom: 0
}

.c-btn__action {
    font-size: 24px;
    margin-top: 10px;
    line-height: 33px;
    letter-spacing: .43px
}
.c-btn__chance{
    font-size: 12px;
    line-height: 17px;

    padding: 0 8px;
    margin-top: 8px;
    border-radius: 8px;
    background: rgba(0,0,0,.2)
}
.l-scene__user {
    color: #fff;
    display: -webkit-box;
    display: -webkit-flex;
    display: -moz-box;
    display: flex;
    font-size: 14px;
    line-height: 20px;
    margin: 10px 15px 0;
    -webkit-box-pack: justify;
    -webkit-justify-content: space-between;
    -moz-box-pack: justify;
    justify-content: space-between
}

.l-scene__user .c-user__record {
    color: #fff;
    text-decoration: underline
}
.c-result__popup {
    width: 305px;
    background: #fff;
    border-radius: 8px;
    overflow: visible;
    background-image: url(../../../assets/image/home/home-marketwheel-popup.png);background-repeat:no-repeat;background-size:100%;background-position:top center;
    z-index: 2001;
}
.c-result__container {
    position: relative
}

.c-result__title {
    color: #fff;
    font-size: 38px;
    font-weight: 500;
    padding-top: 32px;
    text-align: center;
    letter-spacing: .63px
}

.l-result__prize {
    display: -webkit-box;
    display: -webkit-flex;
    display: -moz-box;
    display: flex;
    margin: 19px 21px;
    border-radius: 8px;
    -webkit-box-align: center;
    -webkit-align-items: center;
    -moz-box-align: center;
    align-items: center;
    -webkit-box-orient: vertical;
    -webkit-box-direction: normal;
    -webkit-flex-direction: column;
    -moz-box-orient: vertical;
    -moz-box-direction: normal;
    flex-direction: column;
    padding: 30px 0 23px;
    -webkit-box-pack: center;
    -webkit-justify-content: center;
    -moz-box-pack: center;
    justify-content: center;
    background-color: #fff;
    box-shadow: 0 0 10px 0 rgba(199,73,67,.28)
}

.c-prize__icon {
    width: 50px;
    height: 50px;
    background-size:100%;
    background-repeat:no-repeat;
    background-position: center;
}
.c-prize__icon.award-type-0{background-image:url(../../../assets/image/home/home-marketwheel-0.png)}
.c-prize__icon.award-type-1{background-image:url(../../../assets/image/home/home-marketwheel-1.png)}
.c-prize__icon.award-type-2{background-image:url(../../../assets/image/home/home-marketwheel-2.png)}
.c-prize__icon.award-type-3{background-image:url(../../../assets/image/home/home-marketwheel-3.png)}
.c-prize__name {
    color: #333;
    font-size: 14px;
    margin-top: 15px;
    font-weight: 500;
    line-height: 20px
}
.popup{border-radius:1rem;
    .button-wrapper{padding:1rem;padding-top:0}
}
</style>
