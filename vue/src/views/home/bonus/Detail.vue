<template>
  <div class="distributor-article-list">
    <div class="common-header-wrap">
      <mt-header :title="bonus.bonus_name" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
      </mt-header>
    </div>
    <div class="detail_bg">
      <div class="red_packet">
        <span :class="red_packet_bg"></span>
        <button @click="receiveBonus(bonus)" v-if="!message">领取红包</button>
        <div class="red-jg" v-if="message">
          <h1>恭喜您！</h1>
          <h5>{{ message }}</h5>
        </div>
      </div>
      <p class="tips">领取的红包将转入到商城预存款</p>
      <p class="rule" @click="goMemberBonusreceive">查看领取记录</p>
        <div class="bonus-info" v-if="bonus">
            <p>红包名称：{{bonus.bonus_name}}</p>
            <p>红包祝福语：{{bonus.bonus_blessing}}</p>
            <p>红包有效时间：{{bonus.bonus_begintime_text}}至{{bonus.bonus_endtime_text}}</p>
        </div>
    </div>
  </div>
</template>

<script>
import { getBonusDetail, receiveBonus } from '../../../api/homeBonus'
import { Toast } from 'mint-ui'
export default {
  name: 'Bonusdetail',
  data () {
    return {
      bonus: '',
      bonusreceive_list: '',
      message: '',
      red_packet_bg: 'bg-w' // 背景图
    }
  },
  created: function () {
    let bonus_id = this.$route.query.bonus_id
    getBonusDetail(bonus_id)
      .then(res => {
        this.bonus = res.result.bonus
        this.bonusreceive_list = res.result.bonusreceive_list
      })
      .catch(function (error) {
        Toast(error.message)
      })
  },
  methods: {
    receiveBonus (bonus) {
      receiveBonus(bonus.bonus_id)
        .then(res => {
          this.message = res.message
          this.red_packet_bg = 'bg-y'
          // Toast(res.message)
        })
        .catch(function (error) {
          Toast(error.message)
        })
    },
    goMemberBonusreceive () {
      this.$router.push({ name: 'MemberBonusreceive' })
    }
  }
}
</script>

<style scoped>
.detail_bg {
  background: url("../../../assets/image/home/home-bonus-bg.png");
  background-size: 100% 100%;
  position: absolute;
  width: 100%;
  height: 100%;
  top: 2rem;
  left: 0;
}
.red_packet {
  margin: 2rem 20% 1rem 20%;
  width: 60%;
  position: relative;
  padding-top: 100%;
}
.red_packet span {
  width: 100%;
  height: 100%;
  background-size: 100%;
  background-position: center;
  background-repeat: no-repeat;
  top: 0;
  left: 0;
  position: absolute;
}
.red_packet button {
  position: absolute;
  top: 38%;
  left: 30%;
  font-size: 0.7rem;
  width: 40%;
  height: 24%;
  display: block;
  border-radius: 100%;
  background: #fdc339;
  color: #fff;
  border: none;
}
.red_packet .red-jg {
  position: absolute;
  top: 40%;
  text-align: center;
  width: 100%;
}
.red_packet .red-jg > h1 {
  font-size: 1rem;
  color: #ffc000;
  line-height: 2rem;
}
.red_packet .red-jg > h5 {
  color: #fff;
  font-size: 0.7rem;
}
.bg-w {
  background-image: url("../../../assets/image/home/home-bonus-red-w.png");
}
.bg-y {
  background-image: url("../../../assets/image/home/home-bonus-red-y.png");
}
.tips {
  font-size: 0.7rem;
  color: #bf6b1d;
  text-align: center;
}
.rule {
  font-size: 0.6rem;
  color: #bf6b1d;
  text-align: center;
  margin-top: 0.5rem;
}
.bonus-info{color: #bf6b1d;padding:0 1rem;margin-top:1rem;font-size:.6rem;line-height:1rem;}
.bonus-info p{}

</style>
