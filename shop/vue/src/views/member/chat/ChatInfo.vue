<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header :title="title" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
                <mt-button slot="right" @click="historyVisible=true">聊天记录</mt-button>
            </mt-header>
        </div>
        <div class="dstouch-chat-con" id="dstouch-chat-con">
            <div v-for="(item,index) in msg_list">

                <chat-message :item="getMessage(item,index,0)" :t_id="t_id" :memberInfo="memberInfo" :userInfo="userInfo"></chat-message>
            </div>
        </div>
        <div class="dstouch-chat-bottom">
            <div class="chat-input-layout"> <span class="open-smile"><a class="iconfont" @click="open_smile">&#xe6cd;</a></span>
                <div class="input-box">
                    <input type="text" v-model="msg" @blur.prevent="inputLoseFocus">
                    <mt-button class="submit" size="small" type="danger" @click="submit">发送</mt-button>
                </div>
            </div>
            <div class="chat-smile-layout" v-show="smile_show">
                <ul>
                    <li v-for="(smile,index) in smilies_array" @click="addSmile(index)"><img :title="smile.text" :alt="smile.text" :src="getSmile(smile.image)"></li>
                </ul>
            </div>
        </div>

        <!--聊天记录-->
        <mt-popup v-model="historyVisible" position="right" class="common-popup-wrapper">
            <div class="common-header-wrap">
                <mt-header title="聊天记录" class="common-header">
                    <mt-button slot="left" icon="back" @click="historyVisible=false"></mt-button>
                </mt-header>
            </div>
            <div class="common-popup-content">
                <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
                    <div v-if="historyList && historyList.length">
                        <chat-message v-for="(item,index) in historyList" :item="getMessage(item,index,1)" :t_id="t_id" :memberInfo="memberInfo" :userInfo="userInfo"></chat-message>
                    </div>
                    <empty-record v-else-if="historyList && !historyList.length"></empty-record>
                </div>
            </div>
        </mt-popup>
    </div>
</template>

<script>
import ChatMessage from './ChatMessage'
import EmptyRecord from '../../../components/EmptyRecord'
import { Toast, Indicator } from 'mint-ui'
import { mapState,mapActions } from 'vuex'
import { loadScript } from '../../../util/common'
import { getChatInfo, addChat, getChatHistory } from '../../../api/memberChat'
export default {
  data () {
    return {
      params: { 'page': 0, 'per_page': 20 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      historyVisible: false,
      historyList: false,
      title: '',
      t_id: this.$route.query.t_id,
      t_name: this.$route.query.t_name,
      goods_id: this.$route.query.goods_id,
      memberInfo: false,
      userInfo: false,
      smile_show: false,
      socket_connect: 0,
      socket: false,
      msg_list: [],
      msg: '',
      smilies_array: {
        ':smile:': { image: 'smile.gif', text: '微笑' },
        ':sad:': { image: 'sad.gif', text: '难过' },
        ':biggrin:': { image: 'biggrin.gif', text: '呲牙' },
        ':cry:': { image: 'cry.gif', text: '大哭' },
        ':huffy:': { image: 'huffy.gif', text: '发怒' },
        ':shocked:': { image: 'shocked.gif', text: '惊讶' },
        ':tongue:': { image: 'tongue.gif', text: '调皮' },
        ':shy:': { image: 'shy.gif', text: '害羞' },
        ':titter:': { image: 'titter.gif', text: '偷笑' },
        ':sweat:': { image: 'sweat.gif', text: '流汗' },
        ':mad:': { image: 'mad.gif', text: '抓狂' },
        ':lol:': { image: 'lol.gif', text: '阴险' },
        ':loveliness:': { image: 'loveliness.gif', text: '可爱' },
        ':funk:': { image: 'funk.gif', text: '惊恐' },
        ':curse:': { image: 'curse.gif', text: '咒骂' },
        ':dizzy:': { image: 'dizzy.gif', text: '晕' },
        ':shutup:': { image: 'shutup.gif', text: '闭嘴' },
        ':sleepy:': { image: 'sleepy.gif', text: '睡' },
        ':hug:': { image: 'hug.gif', text: '拥抱' },
        ':victory:': { image: 'victory.gif', text: '胜利' },
        ':sun:': { image: 'sun.gif', text: '太阳' },
        ':moon:': { image: 'moon.gif', text: '月亮' },
        ':kiss:': { image: 'kiss.gif', text: '示爱' },
        ':handshake:': { image: 'handshake.gif', text: '握手' }
      }
    }
  },
  beforeDestroy: function () {
    this.socket_connect = 0
  },
  components: {
    ChatMessage: ChatMessage,
    EmptyRecord: EmptyRecord
  },
  computed: {
    ...mapState({
        config: state => state.config.config,
      user: state => state.member.info,
    })
  },
    updated:function(){
        this.$nextTick(function(){
            var div = document.getElementById('dstouch-chat-con');
            div.scrollTop = div.scrollHeight;
        })
    },
  mounted () {
  },
  created () {
    if (!this.t_id) {
      Toast('参数错误')
      this.$router.go(-1)
    }
    if (this.config.node_site_use!='1' || !this.config.node_site_url) {
      Toast('未开启即时聊天')
      this.$router.go(-1)
    }
      this.fetchConfig({}).then(
          response => {
          },
          error => {
              Toast(error.message)
          }
      )
    let _this = this
    loadScript('chat',this.config.node_site_url + '/socket.io/socket.io.js',function () {
      getChatInfo(_this.t_id, _this.goods_id).then(res => {
        let memberInfo = res.result.member_info
        let userInfo = res.result.user_info
        let chat_goods = res.result.chat_goods


        _this.memberInfo = memberInfo
        _this.userInfo = userInfo
        _this.title = userInfo.store_name != '' ? userInfo.store_name : userInfo.member_name
        var a = {}
        a['u_id'] = memberInfo.member_id
        a['u_name'] = memberInfo.member_name
        a['avatar'] = memberInfo.member_avatar
        a['s_id'] = userInfo.member_id
        a['s_name'] = userInfo.member_name
        a['s_avatar'] = userInfo.store_avatar

          if (chat_goods) {
              let goods_info={goods_id:chat_goods.goods_id,goods_name:chat_goods.goods_name,goods_price:chat_goods.goods_price,goods_image:chat_goods.pic24}
              let msg = '[goods]'+JSON.stringify(goods_info)+'[/goods]'
              _this.submit(msg)
          }
        let socket = io(_this.config.node_site_url, {
          path: '/socket.io',
          reconnection: false
        })
        socket.on('connect', function () {
          _this.socket_connect = 1
          socket.emit('update_user', a)
          socket.on('get_msg', function (msg_list) {
            _this.filterMsg(msg_list)
          })
          socket.on('disconnect', function () {
            _this.socket_connect = 0
          })
        })
        _this.socket = socket
      }).catch(function (error) {
        Toast(error.message)
      })
    })
  },
  watch: {

  },
  methods: {
      ...mapActions({
          fetchConfig: 'fetchConfig'
      }),
// 解决调起手机软键盘页面被顶到底部再关闭软键盘页面底部留白的问题
      inputLoseFocus() {
        setTimeout(() => {
          window.scrollTo(0, 0);
        }, 100);
      },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getHistoryList(true)
      }
    },

    getHistoryList () {
      Indicator.open()

      getChatHistory(this.params, this.t_id).then(res => {
        Indicator.close()
        if (res.result.hasmore) {
          this.isMore = true
        } else {
          this.isMore = false
        }

        let temp = res.result.list
        if (temp) {
          if (!this.historyList) {
            this.historyList = temp
          } else {
            this.historyList = this.historyList.concat(temp)
          }
        }
      }).catch(function (error) {
        Indicator.close()
        Toast(error.message)
      })
    },
    getMessage (item, index, type) {
        this.getGoods(item, index, type)
      this.getTime(item, index, type)
      if (type) {
        return this.historyList[index]
      } else {
        return this.msg_list[index]
      }
    },
    getGoods (item, index, type) {
        let goods_id=0
        if(item.chat_goods){
            goods_id=item.chat_goods.goods_id
        }
      if (type) {
        this.historyList[index]['if_show_goods'] = false
        this.historyList[index]['show_goods'] = goods_id
        if(goods_id){
            if (index) {
                if (this.historyList[index - 1]['show_goods'] != goods_id) {
                    this.historyList[index]['if_show_goods'] = true
                }
            } else {
                this.historyList[index]['if_show_goods'] = true
            }
        }else{
            if(index){
                this.historyList[index]['show_goods'] = this.historyList[index - 1]['show_goods']
            }
        }
      } else {
        this.msg_list[index]['if_show_goods'] = false
        this.msg_list[index]['show_goods'] = goods_id
        if(goods_id){
            if (index) {
                if (this.msg_list[index - 1]['show_goods'] != goods_id) {
                    this.msg_list[index]['if_show_goods'] = true
                }
            } else {
                this.msg_list[index]['if_show_goods'] = true
            }
        }else{
            if(index){
                this.msg_list[index]['show_goods'] = this.msg_list[index - 1]['show_goods']
            }
        }
      }
    },
    getTime (item, index, type) {
        let time=item.chatlog_addtime
      if (type) {
        this.historyList[index]['if_show_time'] = false
        if (index) {
          if (Math.abs(this.historyList[index - 1]['show_time'] - time) > 10) {
            this.historyList[index]['show_time'] = time
            this.historyList[index]['if_show_time'] = true
          } else {
            this.historyList[index]['show_time'] = this.historyList[index - 1]['show_time']
          }
        } else {
          this.historyList[index]['show_time'] = time
          this.historyList[index]['if_show_time'] = true
        }
      } else {
        this.msg_list[index]['if_show_time'] = false
        if (index) {
          if (Math.abs(this.msg_list[index - 1]['show_time'] - time) > 10) {
            this.msg_list[index]['show_time'] = time
            this.msg_list[index]['if_show_time'] = true
          } else {
            this.msg_list[index]['show_time'] = this.msg_list[index - 1]['show_time']
          }
        } else {
          this.msg_list[index]['show_time'] = time
          this.msg_list[index]['if_show_time'] = true
        }
      }
    },
    open_smile () {
      this.smile_show = !this.smile_show
    },
    addSmile (code) {
      this.msg += code
      this.smile_show = false
    },
    submit (msg='') {
      if (msg == '' && this.msg == '') {
        Toast('请填写内容')
        return
      }
      if(this.msg){
          msg = this.msg
      }

      addChat({ t_id: this.t_id, t_name: this.userInfo.member_name, t_msg: msg }).then(res => {
        this.msg = ''
        let msg = res.result.msg
        // msg['avatar']=this.memberInfo.member_avatar
        this.msg_list.push(msg)
        if (this.socket_connect) {
          this.socket.emit('send_msg', msg)
        }
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    getSmile (smile) {
      return process.env.VUE_APP_SITE_URL + '/static/plugins/js/smilies/images/' + smile
    },
    filterMsg (msg_list) {
      let msg_id = false
      for (var k in msg_list) {
        let msg = msg_list[k]
        if (msg.t_id != this.user.member_id || msg.f_id != this.t_id) {
          continue
        }
        msg_id = k
        // msg['avatar']=this.userInfo.store_id?this.userInfo.store_avatar:this.userInfo.member_avatar
        this.msg_list.push(msg)
      }
      if (msg_id) {
        if (this.socket_connect) {
          this.socket.emit('del_msg', {
            max_id: msg_id,
            f_id: this.t_id
          })
        }
      }
    }

  }

}
</script>

<style scoped lang="scss">

    .dstouch-chat-con { position: absolute; z-index: auto; max-height: 100%; left: 0; right: 0; bottom: 2rem; overflow-x: hidden;  overflow-y: auto;box-sizing: border-box;padding-top: 4rem;}

    .dstouch-chat-bottom { position: absolute; z-index: auto; left: 0; right: 0; bottom: 0;}
    .chat-input-layout { position: relative; z-index: 1; display: block; height: 2rem; background-color: rgba(255,255,255,0.90)}
    .chat-input-layout .open-smile { display: block; width: 1.5rem; height: 1.5rem; padding: 0.25rem;}
    .chat-input-layout .open-smile a { display: block; width: 1.5rem; height: 1.5rem; font-size:1.5rem;color:#6eb6eb}
    .chat-input-layout .input-box { position: absolute; z-index: 1; top: 0.24rem; right: 0.4rem; bottom: 0.24rem; left: 2rem; background: #F5F5F5; border: solid 0.05rem #EEE; border-radius: 0.2rem;}
    .chat-input-layout .input-box input[type="text"] { background-color: transparent; border: none; width: 100%; height: 100%; padding:.1rem 3rem .1rem .1rem; font-size: 0.6rem;box-sizing:border-box;display: block;}
    .chat-input-layout .input-box .submit { position: absolute; z-index: 1; top: 0; right:0; display: block; height: 1.4rem; }
    .chat-smile-layout { display: block; width: 100%; height: 4.2rem; background-color: #FAFAFA; border-top: solid 0.05rem #DDD;}
    .chat-smile-layout ul { font-size: 0;}
    .chat-smile-layout ul li { display: inline-block; width: 12.5%; height: 1rem; padding: 0.3rem 0 0 0; text-align: center;  vertical-align: middle;}
    .chat-smile-layout ul li img { display: inline-block; height: 100%; vertical-align: middle;}
    .common-popup-content{background:#f7f7f7;overflow-x:hidden}

</style>
