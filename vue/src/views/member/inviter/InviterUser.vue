<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header title="推广会员" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
      </mt-header>
    </div>
    <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
      <div  v-if="userList && userList.length">
        <div class="inviter-item" v-for="(v,k) in userList" :key="v.member_id">
          <div class="avatar">
            <img :src="v.member_avatar" />
          </div>
          <div class='inviter'>
            <div class='users'>
              <strong>{{v.member_name}}</strong>
              <span v-for="(i_u,n) in v.inviters" :key="n">->{{i_u}}</span>
            </div>
            <div class='time'>注册时间：{{v.member_addtime}}</div>
            <div class='time'>最后登录：{{v.member_logintime}}</div>
          </div>
        </div>
      </div>
      <empty-record v-else-if="userList && !userList.length"></empty-record>
    </div>
  </div>
</template>

<script>
import { getInviterUser } from '../../../api/memberInviter'
import EmptyRecord from '../../../components/EmptyRecord'
import { Toast, Indicator } from 'mint-ui'
export default {
  name: 'MemberInviterUser',
  components: {
    EmptyRecord
  },
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      userList: false
    }
  },
  created () {

  },

  mounted () {

  },
  methods: {
    getUserList (ispush) {
      Indicator.open()
      let params = this.params
      getInviterUser(params).then(res => {
        Indicator.close()
        if (res) {
          if (ispush && this.userList) {
            this.userList = this.userList.concat(res.result.list)
          } else {
            this.userList = res.result.list
          }
          if (res.result.hasmore) {
            this.isMore = true
          } else {
            this.isMore = false
          }
        }
      })
    },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getUserList(true)
      }
    }
  }
}
</script>

<style scoped lang="scss">
.inviter-item{display:flex;padding:.5rem;background:#fff;border-bottom:1px solid #e4e4e4;
  .avatar{width:4.5rem;
    img{width:4rem}
  }
  .inviter{flex:1;font-size:.7rem;
    .users{font-size:.75rem;margin-bottom:1rem;margin-top:.5rem}
    .time{color:gray;margin-top:.2rem}
  }
}
</style>
