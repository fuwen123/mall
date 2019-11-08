<!-- Time.vue -->
<template>
	<div
		class="show-promotions-time"
		v-if="mansong_info"
	>
		<div class="time-body">
			<span class="title">满立减</span>
			<div>
				<span>{{ day }}</span
				>&nbsp;天&nbsp;<span>{{ hours }}</span
				>&nbsp;时&nbsp;<span>{{ minute }}</span
				>&nbsp;分&nbsp;<span>{{ second }}</span
				>&nbsp;秒
			</div>
		</div>
	</div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
export default {
  data () {
    return {
      flag: false,
      day: '',
      hours: '',
      minute: '',
      second: '',
      time: ''
    }
  },

  computed: {
    ...mapState({
      mansong_info: state => state.goodsdetail.mansongInfo
    })
  },

  mounted () {
    this.time = setInterval(() => {
      if (this.flag == true) {
        clearInterval(this.time)
      } else {
        this.timeDown()
      }
    }, 1000)
  },

  methods: {
    /*
	 * timeDown: 倒计时
	 */
    timeDown () {
      if (
        this.mansong_info
      ) {
        const endTime = new Date(this.mansong_info.mansong_endtime * 1000)
        const nowTime = new Date()
        let leftTime = parseInt((endTime.getTime() - nowTime.getTime()) / 1000)
        this.day = parseInt(leftTime / (24 * 60 * 60))
        this.hours = this.formate(parseInt((leftTime / (60 * 60)) % 24))
        this.minute = this.formate(parseInt((leftTime / 60) % 60))
        this.second = this.formate(parseInt(leftTime % 60))
        if (leftTime <= 0) {
          this.flag = true
          this.$emit('time-end')
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

<style lang="scss" scoped>
.show-promotions-time {
	margin: 0.4rem 0 0 0;
	div.time-body {
		height: 2.5rem;
		background: rgba(255, 255, 255, 1);
		padding: 0 0.75rem;
		line-height: 2.5rem;
		span.title {
			font-size: 0.7rem;
			color: $primaryColor;
		}
		div {
			float: right;
			font-size: 0.6rem;
			color: #abaeb3;
			span {
				color: $primaryColor;
				padding: 0.1rem 0.15rem;
				border-radius: 0.1rem;
				border: 1px solid #adafb3;
			}
		}
	}
}
</style>
