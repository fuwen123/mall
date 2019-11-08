<template>
  <div class="container">

    <div class="header"
         :style="{backgroundImage:'url('+store.mb_title_img+')'}">
      <div class="content-wrapper">
        <div class="avatar">
          <img :src="store.store_avatar">
        </div>
        <div class="content">
          <div class="store_name">{{store.store_name}}</div>
          <div class="follow_number">已经有{{store.store_collect}}人收藏</div>
        </div>
        <div class="follow_panel">
          <div class="follow_button"
               @click="toggleFavorite">
            <i class="iconfont"
               :class="{'active':store.is_favorate}"
               v-html="favoriteIco"></i>
            {{favoriteName}}
          </div>
        </div>
      </div>
      <div class="store_credit">
        <ul>
          <li>
            <span class="text">{{deliverycredit_text}}</span>
            <span class="credit store_deliverycredit">{{deliverycredit_credit}}分</span>
          </li>
          <li>
            <span class="text">{{desccredit_text}}</span>
            <span class="credit store_desccredit">{{desccredit_credit}}分</span>
          </li>
          <li>
            <span class="text">{{servicecredit_text}}</span>
            <span class="credit store_servicecredit">{{servicecredit_credit}}分</span>
          </li>
        </ul>
      </div>
      <div class="shopping-info-nums">
        <ul>
          <li>
            <p class="nums">{{store.goods_count}}</p>
            <p class="text">全部商品</p>
          </li>
          <li>
            <p class="nums">{{store.seller_name}}</p>
            <p class="text">店铺掌柜</p>
          </li>
        </ul>
      </div>
      <div class="shopping-about mt-5">
        <div class="qr_code"
             @click="imageVisible=true">
          <label>店铺二维码</label>
          <i class="iconfont">&#xe6e0;</i>
          <div class="qr_code_box">

          </div>
        </div>
        <div class="phone">
          <a :href="'tel:'+ store.store_phone">
            <label>商家电话</label>
            <i class="iconfont">&#xe6d1;</i>
          </a>
        </div>
      </div>
      <div class="store_info mt-5">
        <div><label>公司名称</label><span>{{store.store_company_name}}</span></div>
        <div><label>公司地区</label><span>{{store.area_info}}</span></div>
        <div><label>主营商品</label><span>{{store.store_mainbusiness}}</span></div>
        <div><label>店铺QQ</label><span>{{store.store_qq}}</span></div>
        <div><label>店铺旺旺</label><span>{{store.store_ww}}</span></div>
      </div>
    </div>

    <common-store-footer :store_id="store.store_id"></common-store-footer>
    <mt-popup v-model="imageVisible"
              popup-transition="popup-fade"
              class="middle-popup">
      <h4>{{store.store_name}}</h4>
      <div class="img-new-box"><img :src="qrcodeurl"></div>
      <p>邀请好友扫一扫分享店铺给TA</p>
    </mt-popup>
  </div>
</template>

<script>
import { mapState, mapActions } from 'vuex'
import { Toast } from 'mint-ui'
import CommonStoreFooter from '../common/CommonStoreFooter'
import { getStoreInfo } from '../../../api/homestoredetail'
import { addFavoriteStore, delFavoriteStore } from '../../../api/memberFavorite'
import HeaderMore from '../../../components/HeaderMore'
export default {
  name: 'Storeabout',
  data () {
    return {
      imageVisible: false,
      store: {
        store_id: this.$route.query.id ? this.$route.query.id : ''
      },
      rec_goods_list: false,
      popupVisible: false, // 弹出更多
      showDot: false,
      deliverycredit_text: '',
      deliverycredit_credit: 0,
      desccredit_text: '',
      desccredit_credit: 0,
      servicecredit_text: '',
      servicecredit_credit: 0,
      qrcodeurl: ''
    }
  },
  components: {
    HeaderMore,
    CommonStoreFooter
  },
  computed: {
    ...mapState({
      config: state => state.config.config,
      token: state => state.member.token,
      isOnline: state => state.member.isOnline
    }),
    favoriteIco () {
      return this.store.is_favorate ? '&#xe64d;' : '&#xe64e;'
    },
    favoriteName () {
      return this.store.is_favorate ? '已收藏' : '收藏'
    }
  },
  created () {
    getStoreInfo(
      this.store.store_id, this.token
    ).then((res) => {
      this.store = res.result.store_info
      this.qrcodeurl = process.env.VUE_APP_SITE_URL + '/home/qrcode?url=' + encodeURIComponent(process.envH5_HOST + '/home/storedetail?id=' + this.store.store_id)
      this.rec_goods_list = res.result.rec_goods_list
      this.deliverycredit_text = res.result.store_info.store_credit.store_deliverycredit.text
      this.deliverycredit_credit = res.result.store_info.store_credit.store_deliverycredit.credit
      this.desccredit_text = res.result.store_info.store_credit.store_desccredit.text
      this.desccredit_credit = res.result.store_info.store_credit.store_desccredit.credit
      this.servicecredit_text = res.result.store_info.store_credit.store_servicecredit.text
      this.servicecredit_credit = res.result.store_info.store_credit.store_servicecredit.credit
      if (this.store.is_platform_store) {
        this.fetchConfig({}).then(
          response => {
          },
          error => {
            Toast(error.message)
          }
        )
      }
    }).catch(function (error) { })
  },
  methods: {
    ...mapActions({
      fetchConfig: 'fetchConfig'
    }),
    // 弹出更多
    popupMore () {
      if (!this.popupVisible) {
        this.popupVisible = true
      } else {
        this.popupVisible = false
      }
    },
    toggleFavorite () {
      if (!this.store.is_favorate) {
        addFavoriteStore(this.store.store_id).then(
          response => {
            Toast(response.message)
            this.store.is_favorate = !this.store.is_favorate
          },
          error => {
            Toast(error.message)
          }
        )
      } else {
        delFavoriteStore(this.store.store_id).then(
          response => {
            Toast(response.message)
            this.store.is_favorate = !this.store.is_favorate
          },
          error => {
            Toast(error.message)
          }
        )
      }
    }
  }
}
</script>

<style scoped>
.header {
  overflow: hidden;
  position: relative;
  background-position: center;
  background-size: auto 100%;
}
.header .content-wrapper {
  -ms-flex-align: center;
  -webkit-box-align: center;
  align-items: center;
  display: -webkit-box;
  display: -ms-flexbox;
  display: flex;
  padding: 0.8rem 0.5rem;
  position: relative;
  background: #fff;
}
.header .content-wrapper .avatar {
  -ms-flex: 0 0 3rem;
  -webkit-box-flex: 0;
  flex: 0 0 3rem;
  margin-right: 0.4rem;
  width: 3rem;
  border: 1px solid #efefef;
}
.header .content-wrapper .avatar img {
  border-radius: 0.1rem;
  width: 3rem;
  height: 3rem;
}
.header .content-wrapper .content {
  position: relative;
  width: 8rem;
  height: 3rem;
  margin-left: 0.5rem;
}
.header .content-wrapper .content .store_name {
  font-size: 0.75rem;
  margin-bottom: 0.4rem;
}
.header .content-wrapper .content .follow_number {
  line-height: 0.7rem;
  font-size: 0.7rem;
  margin-top: 0.1rem;
  color: #999;
}
.follow_panel {
  position: absolute;
  top: 1rem;
  right: 2rem;
}
.follow_panel .follow_button {
  display: block;
  background: #e93b3d;
  border-radius: 2.5rem;
  padding: 0 0.4rem;
  line-height: 1.2rem;
  font-size: 0.6rem;
  color: #fff;
}
.follow_panel .follow_button i {
  line-height: 1.2rem;
}
.store_credit {
  background: #fff;
  padding: 0px 0.5rem 10px;
}
.store_credit ul li {
  display: inline-block;
  width: 33%;
  text-align: center;
}
.store_credit ul li .text {
  font-size: 0.6rem;
}
.store_credit ul li .credit {
  font-size: 0.6rem;
  margin-left: 0.2rem;
}
.store_credit ul li .store_deliverycredit {
  color: #ec5151;
}
.store_credit ul li .store_desccredit {
  color: #13ab53;
}
.store_credit ul li .store_servicecredit {
  color: #f447c9;
}
.shopping-info-nums {
  background: #fff;
  border-top: 1px solid #f3f3f3;
  padding: 10px 0;
}
.shopping-info-nums ul li {
  width: 50%;
  text-align: center;
  display: inline-block;
}
.shopping-info-nums ul li .nums {
  font-size: 0.8rem;
  color: #333;
}
.shopping-info-nums ul li .text {
  font-size: 0.5rem;
  color: #999;
}
.shopping-about {
  background: #fff;
  padding-top: 25px;
}
.shopping-about div {
  border-top: 1px solid #f3f3f3;
  padding: 0 0.5rem;
  height: 2rem;
  line-height: 2rem;
}
.shopping-about label {
  width: 15%;
  color: #999;
  text-align: center;
  font-size: 0.65rem;
  vertical-align: middle;
}
.shopping-about i {
  float: right;
  font-size: 1.1rem;
  color: #999;
}
.store_info {
  background: #fff;
}
.store_info div {
  border-bottom: 1px solid #f3f3f3;
  padding: 7px 0.5rem;
}
.store_info div label {
  width: 15%;
  color: #999;
  text-align: center;
  font-size: 0.65rem;
  vertical-align: middle;
}
.store_info div span {
  width: 68%;
  margin-left: 2%;
  font-size: 0.65rem;
  vertical-align: middle;
}
.header .background {
  -webkit-filter: blur(0.5rem);
  filter: blur(0.5rem);
  height: 100%;
  left: 0;
  position: absolute;
  top: 0;
  width: 100%;
  z-index: -1;
}
.middle-popup h4 {
  font-size: 0.75rem;
  text-align: center;
  padding: 0.6rem;
  border: 1px solid #eee;
  color: #444;
}
.middle-popup .img-new-box {
  background: #fff;
  padding: 0.75rem;
  margin: 0.5rem 0.65rem;
  height: auto;
}
.middle-popup .img-new-box img {
  width: 100%;
}
.middle-popup p {
  font-size: 0.65rem;
  color: #777;
  text-align: center;
  padding: 0.4rem;
  border-top: 1px solid #eee;
}
</style>
