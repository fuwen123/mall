<template>
    <div class="home-marketegg">
        <div class="top-main">
            <div class="tops"><img src="../../../assets/image/home/home-marketegg-logo.png"></div>
            <div class="tops_pic"><img src="../../../assets/image/home/home-marketegg-lamp.gif"></div>
        </div>
        <div class="center_sta ">
            <section class="stage ">
                <div id="wide-wrapper">
                    <div id="carousel" class="carousel">
                        <div class="carousel-item" @click="selectEgg(1)">
                            <img class="selected" v-if="active==1" src="../../../assets/image/home/home-marketegg-1.gif">
                            <img v-else src="../../../assets/image/home/home-marketegg-0.png">
                        </div>
                        <div class="carousel-item" @click="selectEgg(2)">
                            <img class="selected" v-if="active==2" src="../../../assets/image/home/home-marketegg-1.gif">
                            <img v-else src="../../../assets/image/home/home-marketegg-0.png">
                        </div>
                        <div class="carousel-item" @click="selectEgg(3)">
                            <img class="selected" v-if="active==3" src="../../../assets/image/home/home-marketegg-1.gif">
                            <img v-else src="../../../assets/image/home/home-marketegg-0.png">
                        </div>
                    </div>
                </div>
            </section>
        </div>
        <mt-button type="primary" size="large" class="large-btn" @click="reset" v-if="canDraw">再来一次</mt-button>
        <mt-button type="primary" disabled="" size="large" class="large-btn" v-else>机会用完了</mt-button>

        <div class="info-block">
            <div class="info-area">
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
</template>

<script>
import { getMarketmanageInfo, addMarketmanagelog } from '../../../api/homeMarketmanage'
import { getMemberInfo } from '../../../api/member'
import { Toast, MessageBox } from 'mint-ui'
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {
      active: false,
      marketmanage_id: 0,
      marketmanage_info: false,
      marketmanageaward_list: false,
      isResult: false,
      drawResult: false,
      drawTitle: '谢谢参与',
      drawContent: '哎呀，肯定姿势不对！',
      canDraw: false,
      countLeft: 0,
      count: 0,
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

    getMarketmanageInfo(this.marketmanage_id, this.token).then(res => {
      this.marketmanage_info = res.result.marketmanage_info
      this.marketmanageaward_list = res.result.marketmanageaward_list
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
    selectEgg (index) {
      if (this.active) return
      if (!this.isOnline) {
        this.$router.push({ name: 'HomeMemberLogin' })
        return
      }
      if (!this.canDraw) return
      this.active = index
      addMarketmanagelog(this.marketmanage_id).then(res => {
        this.canDraw = res.result.can_draw
        this.countLeft = res.result.count_left

        if (res.result.draw_result) { // 中奖
          this.drawResult = true
          this.drawInfo = res.result.draw_info
          this.drawTitle = res.result.draw_info.marketmanageaward_level + '等奖'
          this.drawContent = res.result.draw_info.marketmanageaward_text
        }

        MessageBox.alert(this.drawContent, this.drawTitle)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    reset () {
    // 重置
      this.active = false
      this.drawResult = false
      this.drawTitle = '谢谢参与'
      this.drawContent = this.marketmanage_info.marketmanage_failed
    }
  },
  watch: {

  },

  mounted () {

  }
}
</script>

<style scoped lang="scss">
    .home-marketegg{position: fixed;width:100%;height:100%;background-color: #FFE881;overflow-y:auto;}
    .top-main {
        width: 100%;
        margin: 0rem auto 0;
        text-align: center;
        position: relative;
        z-index: 80;
        background: url(../../../assets/image/home/home-marketegg-bg.png) no-repeat top center;
        background-size: 100%;
        height: 14rem;
    }
    .tops {
        width: 100%;
        margin: 0 auto 0;
        text-align: center;
        position: absolute;
        top: 0%;
        left: 0%;
        z-index: 9;
    }
    .tops img {
        width: 80%;
        margin: 15% auto 0;
    }
    .tops_pic {
        width: 100%;
        margin: 0 auto;
        position: absolute;
        top: 4%;
        left: 0%;
        z-index: 9;
    }
    .tops_pic img {
        width: 60%;
        margin: 0 auto;
    }
    #wide-wrapper {
        width: 100%;
        margin: 0 auto;
    }
    #carousel {
        position: relative;
        width: 80%;
        overflow: hidden;
        padding: 10%;
        display: flex;
        .carousel-item{flex:1;text-align: center;position: relative;height: 5rem;
            img{width:4.5rem;position: absolute;bottom:0;left:50%;margin-left:-2.25rem}
            .selected{width:8rem;margin-left:-4rem}
        }
    }
    .large-btn{width:80%;margin:0 auto}
    .info-block{font-size:.6rem;padding:.5rem;margin:1rem auto;color:#e93c3d;width:80%;border:1px dashed #e93c3d;box-sizing: border-box}
    .info-block ul.activity-info li{padding-bottom: .5rem;}
</style>
