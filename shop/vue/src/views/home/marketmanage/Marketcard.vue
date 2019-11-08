<template>
    <div class="home-marketcard">
        <div class="marketcard-logo"><img src="../../../assets/image/home/home-marketcard-logo.png"></div>
        <div class="scratchcard-bg">
            <div class="scratchcard" :style="`width:${cardWidth}px; height:${cardHeight}px`">
                <canvas @mousedown="handleMouseDown" @mousemove="handleMouseMove" @mouseup="handleMouseUp"
                        @touchstart="handleMouseDown" @touchmove="handleMouseMove" @touchend="handleMouseUp"
                        ref="canvas" class="scratchcard-overlay"></canvas>
                <div v-if="overlayLoaded" class="scratchcard-content">
                    <div v-if="isResult">
                        <div class="result-area" :class="drawResult?'suc':'fail'">
                            <div class="face-area"></div>
                            <div class="result-content-wap">
                                <p class="result-title">{{drawTitle}}</p>
                                <p class="result-content">{{drawContent}}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="marketcard-info" v-if="marketmanage_info">
            <div class="title">活动名称：</div>
            <div class="content">{{marketmanage_info.marketmanage_name}}</div>
            <div class="title">活动介绍：</div>
            <div class="content">{{marketmanage_info.marketmanage_detail}}</div>
            <div class="title">活动有效时间：</div>
            <div class="content">{{marketmanage_info.marketmanage_begintime_text}}至{{marketmanage_info.marketmanage_endtime_text}}</div>
            <div class="title">活动奖项设置：</div>
            <div class="content">
                <div v-for="(marketmanageaward,index) in marketmanageaward_list" :key="index">{{marketmanageaward.marketmanageaward_level}}等奖{{marketmanageaward.marketmanageaward_probability}}%：{{marketmanageaward.marketmanageaward_text}}</div>
            </div>
        </div>
        <div class="btn-wrapper">
            <div class="btn">
                <mt-button size="large" type="primary" @click="reset" v-if="canDraw">再刮一次</mt-button>
                <mt-button size="large" type="primary" disabled="" v-else>机会用完了</mt-button>
            </div>
            <div class="btn">
                <mt-button size="large" type="primary" @click="goLog">刮奖记录</mt-button>
            </div>
        </div>
    </div>
</template>

<script>
import { getMarketmanageInfo, addMarketmanagelog } from '../../../api/homeMarketmanage'
import { Toast } from 'mint-ui'
import { mapState } from 'vuex'
export default {
  data () {
    return {
      marketmanage_id: 0,
      marketmanage_info: false,
      marketmanageaward_list: false,
      overlayLoaded: true,
      isDrawing: false,
      isFinished: false,
      canvas: undefined,
      ctx: undefined,
      lastPoint: undefined,
      brush: new Image(),
      imageUrl: require('../../../assets/image/home/home-marketcard-cover.png'),
      cardWidth: 260,
      cardHeight: 132,
      finishPercent: 70,
      forceReveal: false,
      isScrape: false,
      isResult: false,
      drawResult: false,
      drawTitle: '真遗憾，未中奖',
      drawContent: '哎呀，肯定姿势不对！',
      canDraw: false
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
    initCanvas () {
      this.canvas = this.$refs.canvas
      this.canvas.width = this.cardWidth
      this.canvas.height = this.cardHeight
      this.ctx = this.canvas.getContext('2d')
    },
    drawImage () {
      let image = new Image()
      image.crossOrigin = 'Anonymous'
      image.src = this.imageUrl + '?' + Math.random()
      image.onload = () => {
        this.ctx.drawImage(image, 0, 0, 264, 147)
        this.overlayLoaded = true
      }
    },

    scratchAt (x, y) {
      this.ctx.beginPath()
      this.ctx.arc(x, y, 25, 0, 2 * Math.PI, false)
      this.ctx.fill()
    },
    handleMouseDown (e) {
      if (!this.isOnline) {
        this.$router.push({ name: 'HomeMemberLogin' })
        return
      }
      if (!this.marketmanage_info) {
        return
      }
      this.isDrawing = true
      this.lastPoint = this.getMouse(e, this.canvas)
    },
    handleMouseUp () {
      this.isDrawing = false
    },
    handleMouseMove (e) {
      if (!this.canDraw) return
      if (!this.isDrawing) return
      e.preventDefault()
      const currentPoint = this.getMouse(e, this.canvas)
      const distance = this.distanceBetween(this.lastPoint, currentPoint)
      const angle = this.angleBetween(this.lastPoint, currentPoint)
      let x, y
      for (let i = 0; i < distance; i++) {
        x = this.lastPoint.x + Math.sin(angle) * i
        y = this.lastPoint.y + Math.cos(angle) * i
        this.ctx.globalCompositeOperation = 'destination-out'
        this.scratchAt(x, y)
      }
      this.lastPoint = currentPoint
      this.handlePercentage(
        this.getFilledPercent(this.ctx, this.cardWidth, this.cardHeight, 32)
      )
      if (!this.isScrape) { // 消耗刮卡机会
        this.isScrape = true
        addMarketmanagelog(this.marketmanage_id).then(res => {
          this.canDraw = res.result.can_draw
          this.isResult = true
          if (res.result.draw_result) { // 中奖
            this.drawResult = true
            this.drawTitle = '恭喜你，获得' + res.result.draw_info.marketmanageaward_level + '等奖'
            this.drawContent = res.result.draw_info.marketmanageaward_text
          }
        }).catch(function (error) {
          Toast(error.message)
        })
      }
    },
    handlePercentage (filledInPixels = 0) {
      if (filledInPixels > this.finishPercent) this.reveal()
    },
    reveal () {
      if (!this.isFinished) {
        // this.canvas.parentNode.removeChild(this.canvas)
        // this.$emit('complete')
      }
      this.isFinished = true
    },
    getFilledPercent (ctx, width, height, stride) {
      if (!stride || stride < 1) stride = 1
      const pixels = ctx.getImageData(0, 0, width, height)
      const total = pixels.data.length / stride
      let count = 0
      for (let i = 0; i < pixels.data.length; i += stride) {
        if (parseInt(pixels.data[i], 10) === 0) count++
      }
      return Math.round(count / total * 100)
    },
    getMouse (e, canvas) {
      const { left, top } = canvas.getBoundingClientRect()
      const touch = e.touches && e.touches[0]
      if (touch) {
        return { x: touch.clientX - left, y: touch.clientY - top }
      } else {
        const scrollTop = window.pageYOffset || document.documentElement.scrollTop
        const scrollLeft = window.pageXOffset || document.documentElement.scrollLeft
        return { x: e.pageX - left - scrollLeft, y: e.pageY - top - scrollTop }
      }
    },
    distanceBetween (point1, point2) {
      return Math.sqrt(
        Math.pow(point2.x - point1.x, 2) + Math.pow(point2.y - point1.y, 2)
      )
    },
    angleBetween (point1, point2) {
      return Math.atan2(point2.x - point1.x, point2.y - point1.y)
    },
    reset () {
      // 重置
      this.isScrape = false
      this.drawResult = false
      this.isFinished = false
      this.isResult = false
      this.initCanvas()
      this.drawImage()
      this.drawTitle = '真遗憾，未中奖'
      this.drawContent = this.marketmanage_info.marketmanage_failed
    }
  },
  watch: {
    forceReveal (val) {
      if (val) this.reveal()
    }
  },
  mounted () {
    this.initCanvas()
    this.drawImage()
  }
}
</script>

<style scoped lang="scss">
    .home-marketcard{background-color:#f81213;background-image:url(../../../assets/image/home/home-marketcard-bg.png);background-repeat:repeat-x;position: fixed;width:100%;height:100%;overflow-y:auto;}
    .marketcard-logo{text-align: center;
        img{width:14rem;margin-top:1rem;}
    }
    .scratchcard-bg{width:274px;height: 132px;padding:7px 0;margin:1rem auto 0;position: relative;background:url(../../../assets/image/home/home-marketcard-wrapper.png) no-repeat;background-size:100%}
    .scratchcard {
        position: relative;
        display: block;
        margin-left:7px;
    }
    .scratchcard > * {

        position: absolute;
        width: 100%;
        height: 100%;
        display: block;
    }
    .scratchcard-overlay {
        z-index: 1;
    }
    .marketcard-info{color:#fff;padding:0 1rem;margin-top:1rem;font-size:.6rem;
        .content{margin-bottom:1rem;}
    }
    .btn-wrapper{display: flex;padding:.75rem;
        .btn{padding:.25rem;flex:1}
    }

    .home-marketcard .result-area {
        background-color:#f1d183;
        position: absolute;
        display: table;
        width: 260px;
        height: 132px;
        line-height: 1.5em;
        color: #e03021;
        text-align: center;
        font-weight: bold
    }

    .home-marketcard .result-area .result-content-wap {
        display: table-cell;
        vertical-align: middle;
        word-break: break-all;
        padding: 0 20px
    }

    .home-marketcard .result-area .face-area {
        position: absolute;
        top: 27px;
        left: 8px;
        width: 76px;
        height: 76px;
        background-size: cover
    }

    .home-marketcard .result-area .result-title {
        font-size: 18px
    }

    .home-marketcard .result-area .result-content {
        font-size: 22px;
        margin-top: 5px
    }

    .home-marketcard .result-area.suc .result-content-wap,.home-marketcard .result-area.fail .result-content-wap {
        width: 150px;
        text-align: left
    }

    .home-marketcard .result-area.suc .face-area {
        background-image: url("../../../assets/image/home/home-marketcard-suc.png")
    }

    .home-marketcard .result-area.suc .result-title {
        font-size: 14px;
        color: #4e3735
    }

    .home-marketcard .result-area.suc .result-content {
        font-size: 18px;
        color: #e03021
    }

    .home-marketcard .result-area.fail .face-area {
        background-image: url("../../../assets/image/home/home-marketcard-fail.png")
    }

    .home-marketcard .result-area.fail .result-title {
        font-size: 16px;
        color: #372f2f
    }

    .home-marketcard .result-area.fail .result-content {
        font-size: 14px;
        color: #584c3c
    }

</style>
