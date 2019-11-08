<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="最近消息" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div>
            <ul class="dstouch-message-list" id="messageList">

                <li v-for="(item,index) in list">
                    <a @click="$router.push({name:'SellerChatInfo',query:{t_id:index,t_name:item.u_name}})">
                    <div class="avatar">
                        <img :src="item.avatar">
                        <sup v-if="item.r_state == 2"></sup>
                    </div>
                    <dl>
                        <dt>{{item.u_name}}</dt>
                        <dd>{{item.t_msg}}</dd>
                    </dl>
                    <time>{{item.time}}</time>
                </a>
                </li>

            </ul>
        </div>
    </div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import { getChatList } from '../../../api/sellerChat'
export default {
  data () {
    return {
      list: false
    }
  },

  created () {
    Indicator.open()
    getChatList().then(res => {
      this.list = res.result.list
      Indicator.close()
    }).catch(function (error) {
      Indicator.close()
      Toast(error.message)
    })
  },
  watch: {

  },
  methods: {

  }

}
</script>

<style scoped lang="scss">

    .dstouch-message-list { background-color: #FFF;}
    .dstouch-message-list li { position: relative; z-index: 1; height: 3rem; border-bottom: solid 0.05rem #EEE; margin: 0 1.5rem 0 3.2rem;}
    .dstouch-message-list li .avatar { position: absolute; z-index: 1; top: 0.4rem; bottom: 0.4rem; left: -2.7rem; display: block; width: 2.2rem; height: 2.2rem; background-color: #EEE; border-radius: 100%;}
    .dstouch-message-list li .avatar img { width: 100%; height: 100%; border-radius: 100%;}
    .dstouch-message-list li .avatar sup { position: absolute; z-index: 1; top: 0; right: 0; width: 0.4rem; height: 0.4rem; background-color: #f23030; border-radius: 100%;}
    .dstouch-message-list li dl { display: block; padding: 0.4rem 0;}
    .dstouch-message-list li dt { display: block; height: 1.2rem; font-size: 0.7rem; line-height: 1.2rem; color: #111;}
    .dstouch-message-list li dd { display: block; height: 1rem; font-size: 0.6rem; line-height: 1rem; color: #666; overflow: hidden; white-space: nowrap; text-overflow: ellipsis;}
    .dstouch-message-list li time { position: absolute; z-index: 1; top: 0.4rem; right: 0.4rem; font-size: 0.55rem; line-height: 0.9rem; color: #BBB;}

</style>
