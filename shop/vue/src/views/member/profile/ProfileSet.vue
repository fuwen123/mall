<template>
    <div class="member-information">
      <div class="common-header-wrap">
        <mt-header title="个人信息" class="common-header">
          <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
      </div>
      <div class="user-avatar iconfont icon-xiangji" ref="upload_btn" @change="uploadAvatar($event)">
        <img :src="user.member_avatar">
        <input type="file" accept="image/jpg,image/png,image/gif,image/bmp,image/jpeg">
      </div>
      <div class="form">
        <mt-field class="menu-item" label="昵称" v-model="member_info.member_nickname" />
        <mt-field class="menu-item" label="QQ" v-model="member_info.member_qq" />
        <mt-field class="menu-item" label="旺旺" v-model="member_info.member_ww" />
        <div @click="onTime">
          <mt-cell class="menu-item"
                   title="出生日期"
                   :value="member_info.member_birthday"
                   is-link>
          </mt-cell>
        </div>
        <mt-button class="ds-button-large" type="primary" @click="submitInformation">保存</mt-button>
      </div>
      <mt-datetime-picker
              ref="timePicker"
              type="date"
              :startDate="new Date('1900/1/1')"
              :endDate="new Date()"
              :value="member_info.member_birthday?(new Date(member_info.member_birthday)):(new Date('1990/1/1'))"
              @confirm="timeConfirm"
      >
      </mt-datetime-picker>
    </div>
</template>

<script>

import { Toast } from 'mint-ui'
import { mapState, mapMutations } from 'vuex'
import { uploadMemberAvatar, updateMemberInfo } from '../../../api/member'
export default {
  components: {

  },
  name: 'MemberProfileSet',
  data () {
    return {
      member_info: {
        member_nickname: '',
        member_qq: '',
        member_ww: '',
        member_birthday: ''
      }
    }
  },
  mounted () {

  },
  computed: {
    ...mapState({
      user: state => state.member.info
    })
  },
  created: function () {
    if (this.user) {
      this.member_info.member_nickname = this.user.member_nickname
      this.member_info.member_qq = this.user.member_qq
      this.member_info.member_ww = this.user.member_ww
      this.member_info.member_birthday = this.user.member_birthday
    }
  },

  methods: {
    ...mapMutations({
      memberEdit: 'memberEdit'
    }),
    timeConfirm (value) {
      this.member_info.member_birthday = value.getFullYear() + '/' + (value.getMonth()+1) + '/' + value.getDate()
    },
    onTime (picker, values) {
      this.$refs.timePicker.open()
    },
    submitInformation () {
      updateMemberInfo(this.member_info).then(res => {
        this.memberEdit(this.member_info)
        Toast(res.message)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    uploadAvatar (event) {
      if (typeof (event.target.files[0]) === 'undefined') {
        return
      }
      let formdata = new FormData()

      formdata.append('memberavatar', event.target.files[0])

      uploadMemberAvatar(formdata).then(res => {
        this.user.avatar = res.result + '?' + Math.floor(Math.random() * 100)
        this.memberEdit({ member_avatar: this.user.avatar })
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  }
}
</script>
<style  lang="scss" scoped>
  .member-information {
    background: #fff;
    .user-avatar {
      position: relative;
      width: 5rem;
      height: 5rem;
      margin: 2rem auto;
      text-align: center;
      img {
        width: 100%;
        height: 100%
      }
      input {
        width: 100%;
        height: 100%;
        position: absolute;
        top: 0;
        left: 0;
        opacity: 0
      }
    }
    .user-avatar::before {
      position: absolute;
      font-size: 1.5rem;
      line-height: 5rem;
      left: 50%;
      margin-left: -.75rem;
      color: rgba(255, 255, 255, 0.5)
    }
    .menu-item {
      border-top: 1px solid #eee
    }
  }
  .ds-button-large{width:96%;margin:1rem 2% 0 2%;}

</style>
