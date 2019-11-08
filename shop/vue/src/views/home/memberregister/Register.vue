<template>
  <div class="container">

  </div>
</template>

<script>
import RegisterByMobile from './RegisterByMobile'
import RegisterByEmail from './RegisterByEmail'
import { mapMutations } from 'vuex'
export default {
  name: 'Register',
  components: {
    RegisterByMobile,
    RegisterByEmail
  },
  data () {
    return {
      currentIndex: 0,
      items: [
        {
          id: 1,
          title: '普通注册'
        },
        {
          id: 2,
          title: '手机注册'
        }

      ]
    }
  },
  created: function () {
    if (this.$route.query.inviter_id) {
      this.memberInviterId({ inviterId: this.$route.query.inviter_id })
    }
  },
  computed: {
    isFirstTab () {
      if (this.currentIndex === 0) {
        return true
      } else {
        return false
      }
    }
  },
  methods: {
    ...mapMutations({
      memberInviterId: 'memberInviterId'
    }),
    goBack () {
      console.log('是触发了你吗')
      this.$router.go(-1)
    },
    getTitle (item) {
      return item ? item.title : ''
    },
    isShowLine (index) {
      return index === this.currentIndex
    },
    onClickItem (index) {
      if (this.currentIndex !== index) {
        this.currentIndex = index
      }
    }
  }
}
</script>

<style scoped lang="scss">
.container {
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: stretch;
  //background-color: #e93b3d;
}

.topList {
  height: 2rem;
  border-bottom: 1px solid #e8eaed;
  .list {
    height: 100%;
    display: flex;
    flex-direction: row;
    justify-content: flex-start;
    align-content: center;
    align-items: stretch;
    background-color: #fff;
  }
  .item {
    flex: 1;
    position: relative;
    display: flex;
    flex-direction: column;
    justify-content: center;
    align-items: center;
  }
  .title {
    text-align: center;
    font-size: 0.7rem;
    color: #404245;
  }
  .active {
    color: $mainColor;
  }
  .normal {
    color: #404245;
  }
  .line {
    position: absolute;
    left: 2.5rem;
    right: 2.5rem;
    bottom: 0;
    height: 0.1rem;
    background-color: #e93b3d;
  }
}
</style>
