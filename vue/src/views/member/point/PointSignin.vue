<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header title="签到送积分" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
      </mt-header>
    </div>
    <div class="point-signin-header">
      <div class="btn-wrapper" :class="{active:!if_signin}">
        <div class="btn">
          <div v-if="if_signin"><div class="title">已签到</div><div class="content" v-if="user.member_signin_days_cycle">连续{{user.member_signin_days_cycle}}天</div><div class="content" v-else>进入新周期</div></div>
          <div v-else @click="goSign">签到</div>
        </div>
      </div>
      <div class="notice">连续签到{{config.points_signin_cycle}}天为一周期，每周期可获得{{config.points_signin_reward}}积分额外奖励，每过一个周期连续签到天数清0</div>
    </div>
    <div class="calendar-wrapper" v-if="day_list">
      <div class="calendar-month"><i class="iconfont" @click="goSearch(0)">&#xe64f;</i><span>{{$moment(search_day).format('YYYY年MM月')}}</span><i class="iconfont" @click="goSearch(1)">&#xe650;</i></div>
      <div class="calendar-title">
        <div class="item"><span>日</span></div>
        <div class="item"><span>一</span></div>
        <div class="item"><span>二</span></div>
        <div class="item"><span>三</span></div>
        <div class="item"><span>四</span></div>
        <div class="item"><span>五</span></div>
        <div class="item"><span>六</span></div>
      </div>
      <div class="calendar-content">
        <div class="item" v-for="n in day_list[0].week" :key="'t'+n"></div>
        <div class="item" v-for="(item,index) in day_list" :key="'c'+index" :class="{active:item.state,'pre-active':(item.day==$moment().format('YYYY-MM-DD')) && !item.state}"><span>{{item.num}}</span></div>
      </div>
    </div>
  </div>
</template>

<script>
import { mapState, mapMutations, mapActions } from 'vuex'
import { getPointSignin, addPointSignin } from '../../../api/memberPoint'
import { getMemberInfo } from '../../../api/member'
import { Toast, Indicator } from 'mint-ui'
export default {
  name: 'MemberPointSignin',

  data () {
    return {
      day_list: false,
      search_day: '',
      if_signin: false
    }
  },
  created () {
    this.getPointSignin()
    this.fetchConfig({}).then(
      response => {
      },
      error => {
        Toast(error.message)
      }
    )
    getMemberInfo().then(res => {
      this.memberUpdate({ info: res.result.member_info })
    }, error => {}
    )
  },
  computed: {
    ...mapState({
      user: state => state.member.info,
      config: state => state.config.config
    })
  },
  mounted () {

  },
  methods: {
    ...mapActions({
      fetchConfig: 'fetchConfig'
    }),
    ...mapMutations({
      memberUpdate: 'memberUpdate',
      memberEdit: 'memberEdit'
    }),
    goSign () {
      addPointSignin().then(res => {
        Toast(res.message)
        this.memberEdit(res.result.member_signin_info)
        this.getPointSignin()
        this.if_signin=true
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    goSearch (type) {
      if (type) { // 后一个月
        this.search_day = this.$moment(this.search_day).add(1, 'months').format('YYYY-MM-DD')
      } else { // 前一个月
        this.search_day = this.$moment(this.search_day).subtract(1, 'months').format('YYYY-MM-DD')
      }
      this.getPointSignin()
    },
    getPointSignin () {
      getPointSignin(this.search_day).then(res => {
        Indicator.close()
        this.if_signin=res.result.if_signin
        this.day_list = res.result.day_list
        this.search_day = this.$moment.unix(res.result.time).format('YYYY-MM-DD')
      }).catch(function (error) {
        Indicator.close()
        Toast(error.message)
      })
    }
  }
}
</script>

<style scoped lang="scss">
  .point-signin-header{margin-bottom:1rem;background:$primaryColor;padding:.5rem;text-align: center;
    .btn-wrapper{height: 4rem;width:4rem;margin:1rem auto;border-radius:50%;background: $primaryColor;border:.3rem solid rgba(255,255,255,.5);overflow: hidden;
      &.active{
        .btn{
          >div{background:$primaryColor;color:rgba(255,255,255,.8);line-height:3.4rem;}
        }
      }
      .btn{background:#fff;width:100%;height:100%;padding:.3rem;box-sizing:border-box;
        >div{border-radius:50%;width:100%;height:100%;margin:0 auto;border:1px solid $primaryColor;box-sizing:border-box;
          .title{font-size:.8rem;color:$primaryColor;margin-top: .7rem;}
          .content{font-size:.6rem;color:$primaryColor}
        }
      }
    }
    .notice{font-size:.6rem;color:#fff;opacity: .8}
  }
.calendar-wrapper{background:#fff;
  .calendar-month{text-align:center;font-size:.6rem;line-height:2rem;border-bottom:1px solid #eee;margin-bottom:.5rem;
    >span{margin: 0 1rem;}
    i{font-size:.6rem;}
  }
  .item{float:left;width:14.28%;height:1.5rem;text-align: center;font-size:.6rem;
    >span{height:1rem;width:1rem;line-height: 1rem;border-radius: 50%;display: block;margin:0 auto;}
    &.pre-active{
      >span{border:1px solid $primaryColor}
    }
    &.active{
      >span{background:$primaryColor;color:#fff}
    }
  }
  .calendar-content,.calendar-title{overflow: hidden;padding:0 .5rem;}

}
</style>
