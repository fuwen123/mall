(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-1c1eff7e","chunk-33c0a371"],{2934:function(t,e,n){"use strict";n.d(e,"b",(function(){return o})),n.d(e,"a",(function(){return r})),n.d(e,"c",(function(){return a}));var i=n("366f"),o=function(t,e){return Object(i["a"])("/Connect/get_sms_captcha","GET",{type:t,phone:e})},r=function(t){return Object(i["a"])("/Seccode/check","POST",{captcha:t})},a=function(t){return Object(i["a"])("/index/getWechatShare","POST",{url:t})}},"386b":function(t,e,n){var i=n("5ca1"),o=n("79e5"),r=n("be13"),a=/"/g,c=function(t,e,n,i){var o=String(r(t)),c="<"+e;return""!==n&&(c+=" "+n+'="'+String(i).replace(a,"&quot;")+'"'),c+">"+o+"</"+e+">"};t.exports=function(t,e){var n={};n[t]=e(c),i(i.P+i.F*o((function(){var e=""[t]('"');return e!==e.toLowerCase()||e.split('"').length>3})),"String",n)}},"3f6a":function(t,e,n){"use strict";n.d(e,"a",(function(){return i}));n("4917");function i(){var t=window.navigator.userAgent.toLowerCase();return"micromessenger"===t.match(/MicroMessenger/i)}},4917:function(t,e,n){"use strict";var i=n("cb7c"),o=n("9def"),r=n("0390"),a=n("5f1b");n("214f")("match",1,(function(t,e,n,c){return[function(n){var i=t(this),o=void 0==n?void 0:n[e];return void 0!==o?o.call(n,i):new RegExp(n)[e](String(i))},function(t){var e=c(n,t,this);if(e.done)return e.value;var s=i(t),u=String(this);if(!s.global)return a(s,u);var l=s.unicode;s.lastIndex=0;var d,f=[],p=0;while(null!==(d=a(s,u))){var h=String(d[0]);f[p]=h,""===h&&(s.lastIndex=r(u,o(s.lastIndex),l)),p++}return 0===p?null:f}]}))},"4afb":function(t,e,n){"use strict";n.r(e);var i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return t.detailInfo&&t.detailInfo.promos&&t.detailInfo.promos.length>0?n("div",[n("mt-popup",{attrs:{position:"bottom","close-on-click-modal":!1},model:{value:t.promoPopstatus,callback:function(e){t.promoPopstatus=e},expression:"promoPopstatus"}},[n("div",{staticClass:"detail-promotions"},[n("div",{staticClass:"header"},[n("h3",[t._v("促销信息")]),n("span",{staticClass:"iconfont",on:{click:function(e){return t.close()}}},[t._v("")])]),n("div",{staticClass:"promotions-body"},t._l(t.detailInfo.promos,(function(e,i){return n("div",{key:i,staticClass:"body-list"},[n("span",{staticClass:"name"},[t._v(t._s(e.name))]),n("span",{staticClass:"title"},[t._v(t._s(e.promo))]),e.desc?n("div",{staticClass:"content"},[n("p",[t._v(t._s(e.desc))])]):t._e()])})),0)])])],1):t._e()},o=[],r=(n("8e6e"),n("ac6a"),n("456d"),n("bd86")),a=n("2f62");function c(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(t);e&&(i=i.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,i)}return n}function s(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?c(n,!0).forEach((function(e){Object(r["a"])(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):c(n).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}var u={data:function(){return{}},props:{promoPopstatus:{type:Boolean,default:!1}},computed:s({},Object(a["d"])({detailInfo:function(t){return t.goodsdetail.detailInfo}})),methods:s({},Object(a["c"])({changePopstatus:"changePopstatus"}),{close:function(){this.changePopstatus(!1)}})},l=u,d=(n("801e"),n("2877")),f=Object(d["a"])(l,i,o,!1,null,"34097d10",null);e["default"]=f.exports},"579c":function(t,e,n){t.exports=n.p+"img/weixin-share.019164ba.png"},7773:function(t,e,n){"use strict";n.r(e);var i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return t.productDetail?n("div",{staticClass:"product-detail-wrapper"},[t.isPreviewPicture?t._e():n("detail-header"),n("detail-body",{attrs:{isStock:t.productDetail.goods_storage}}),t.isPreviewPicture?t._e():n("detail-footer"),t.isPreviewPicture?n("preview-picture",{attrs:{defaultindex:t.swipeId,isshow:t.isPreviewPicture}}):t._e(),t.promoPopstatus?n("promotion-popup",{attrs:{"promo-popstatus":t.promoPopstatus}}):t._e()],1):t._e()},o=[],r=(n("8e6e"),n("ac6a"),n("456d"),n("bd86")),a=(n("5fc6"),n("5af2")),c=n.n(a),s=n("79bd"),u=n("9738"),l=n("8200"),d=n("1d14"),f=n("4afb"),p=n("2f62");function h(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(t);e&&(i=i.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,i)}return n}function b(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?h(n,!0).forEach((function(e){Object(r["a"])(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):h(n).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}var m={data:function(){return{cartNumber:0,productId:this.$route.query.goods_id?this.$route.query.goods_id:"",hideFooter:!1,popupVisible:!0,currentScore:0,bargain_id:this.$route.query.bargain_id}},components:{DetailHeader:s["default"],DetailBody:u["default"],DetailFooter:l["default"],PreviewPicture:d["default"],PromotionPopup:f["default"]},created:function(){var t={};this.bargain_id&&(t["bargain_id"]=this.bargain_id),this.getGoodsDetail({goods_id:this.productId,token:this.token,extra:t}).catch((function(t){c()(t.message)})),this.saveCartState(!1)},computed:Object(p["d"])({productDetail:function(t){return t.goodsdetail.detailInfo},currentProductId:function(t){return t.goodsdetail.currentProductId},token:function(t){return t.member.token},isPreviewPicture:function(t){return t.goodsdetail.isPreviewPicture},swipeId:function(t){return t.goodsdetail.swipeId},promoPopstatus:function(t){return t.goodsdetail.promoPopstatus},config:function(t){return t.config.config}}),mounted:function(){this.$nextTick((function(){}))},beforeRouteUpdate:function(t,e,n){n(),window.location.reload()},methods:b({},Object(p["c"])({saveCartState:"saveCartState"}),{},Object(p["b"])({fetchConfig:"fetchConfig",getGoodsDetail:"getGoodsDetail"}),{loadConfig:function(t,e,n){var i=this;this.fetchConfig().then((function(o){var r=o.config["wechat.web"];r&&i.setWechatConfig(r,t,e,n)}),(function(t){}))},setWechatConfig:function(t,e,n,i){this.wxApi.wxRegister(t,"商品详情",e,n,i)}})},g=m,v=(n("f2608"),n("2877")),w=Object(v["a"])(g,i,o,!1,null,"b3e5a964",null);e["default"]=w.exports},"79bd":function(t,e,n){"use strict";n.r(e);var i=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",[n("div",{staticClass:"ui-detail-header"},[n("span",{staticClass:"iconfont",on:{click:function(e){return t.$router.go(-1)}}},[t._v("")]),n("div",{staticClass:"navbar-wrapper"},t._l(t.data,(function(e,i){return n("div",{key:i,class:{navbar_active:i==t.index},on:{click:function(e){return t.changeEvent(i)}}},[t._v("\n        "+t._s(e.name)+"\n      ")])})),0),n("span",{staticClass:"iconfont right",on:{click:t.popupMore}},[t._v(""),t.showDot?n("div",{staticClass:"dot"}):t._e()])]),n("header-more",{directives:[{name:"show",rawName:"v-show",value:t.popupVisible,expression:"popupVisible"}],attrs:{goods_id:t.goods_id,share_info:t.share_config,showDot:t.showDot}})],1)},o=[],r=(n("8e6e"),n("ac6a"),n("456d"),n("bd86")),a=n("db15"),c=n("883f"),s=n("2f62"),u=n("9e98");function l(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(t);e&&(i=i.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,i)}return n}function d(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?l(n,!0).forEach((function(e){Object(r["a"])(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):l(n).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}var f={data:function(){return{share_config:!1,data:a["header"],popupVisible:!1,showDot:!1}},components:{HeaderMore:u["a"]},computed:d({},Object(s["d"])({detailInfo:function(t){return t.goodsdetail.detailInfo},goods_id:function(t){return t.goodsdetail.currentProductId},index:function(t){return t.goodsdetail.index},isOnline:function(t){return t.member.isOnline}})),created:function(){var t=this;this.changeIndex(0),this.saveNumber(1),this.isOnline&&Object(c["b"])().then((function(e){e.result&&(t.showDot["chat"]=!0)}))},methods:d({},Object(s["c"])({changeIndex:"changeIndex",saveNumber:"saveNumber"}),{popupMore:function(){this.popupVisible?this.popupVisible=!1:this.popupVisible=!0},changeEvent:function(t){this.changeIndex(t)},goBack:function(){this.$router.go(-1)}}),watch:{detailInfo:function(t){t&&(this.share_config={title:t.goods_name,link:window.location.href,imgUrl:t.photos[0],desc:t.goods_advword})}}},p=f,h=(n("9f9e"),n("2877")),b=Object(h["a"])(p,i,o,!1,null,"fda7c9e8",null);e["default"]=b.exports},"7f0a":function(t,e,n){},"801e":function(t,e,n){"use strict";var i=n("cb7e"),o=n.n(i);o.a},"866e":function(t,e,n){"use strict";var i=n("e661"),o=n.n(i);o.a},"883f":function(t,e,n){"use strict";n.d(e,"d",(function(){return o})),n.d(e,"a",(function(){return r})),n.d(e,"c",(function(){return a})),n.d(e,"e",(function(){return c})),n.d(e,"b",(function(){return s}));var i=n("366f"),o=function(t,e){return Object(i["a"])("/Memberchat/get_node_info","POST",{u_id:t,chat_goods_id:e},"member")},r=function(t){return Object(i["a"])("/Memberchat/send_msg","POST",t,"member")},a=function(t,e){return Object(i["a"])("/Memberchat/get_chat_log","POST",{page:t.page,per_page:t.per_page,t_id:e},"member")},c=function(){return Object(i["a"])("/Memberchat/get_user_list","POST",{recent:1},"member")},s=function(){return Object(i["a"])("/Memberchat/get_msg_count","POST",{},"member")}},"9e98":function(t,e,n){"use strict";var i=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"more-box"},[i("div",{staticClass:"more-item",on:{click:function(e){return t.$router.push({name:"HomeIndex"})}}},[i("i",{staticClass:"iconfont"},[t._v("")]),t._v("首页")]),"HomeGoodsdetail"===t.routerName?i("div",{staticClass:"more-item",on:{click:function(e){return t.$router.push({name:"MemberInformForm",query:{goods_id:t.goods_id}})}}},[i("i",{staticClass:"iconfont"},[t._v("")]),t._v("违规举报")]):t._e(),i("div",{staticClass:"more-item",on:{click:function(e){return t.$router.push({name:"HomeSearch"})}}},[i("i",{staticClass:"iconfont"},[t._v("")]),t._v("搜索")]),i("div",{staticClass:"more-item",on:{click:function(e){return t.$router.push({name:"HomeCart"})}}},[i("i",{staticClass:"iconfont"},[t._v("")]),t._v("购物车")]),t.config&&"1"==t.config.node_site_use&&t.config.node_site_url?i("div",{staticClass:"more-item",on:{click:function(e){return t.$router.push({name:"MemberChatList"})}}},[i("i",{staticClass:"iconfont"},[t._v("")]),t._v("聊天消息"),t.showDot&&t.showDot.chat?i("div",{staticClass:"dot"}):t._e()]):t._e(),i("div",{staticClass:"more-item",on:{click:function(e){return t.$router.push({name:"MemberIndex"})}}},[i("i",{staticClass:"iconfont"},[t._v("")]),t._v("我的商城")]),i("div",{staticClass:"more-item",on:{click:t.onShare}},[i("i",{staticClass:"iconfont"},[t._v("")]),t._v("分享")]),i("i",{staticClass:"arrow"}),i("mt-popup",{staticClass:"common-popup-wrapper",attrs:{position:"bottom"},model:{value:t.shareVisible,callback:function(e){t.shareVisible=e},expression:"shareVisible"}},[i("div",{staticClass:"common-header-wrap"},[i("mt-header",{staticClass:"common-header",attrs:{title:"分享至"}},[i("mt-button",{attrs:{slot:"left",icon:"back"},on:{click:function(e){t.shareVisible=!1}},slot:"left"})],1)],1),i("div",{staticClass:"common-popup-content"},[i("div",{staticClass:"share-list"},[i("div",{staticClass:"share-item weixin",on:{click:function(e){t.weixinShareImage=!0}}},[t._v("微信")]),i("div",{staticClass:"share-item copy",on:{click:t.showLink}},[t._v("复制")])])]),t.weixinShareImage?i("div",{staticClass:"weixin-share-wrapper",style:t.getWrapperStyle,on:{click:function(e){t.weixinShareImage=!1}}},[i("img",{staticClass:"weixin-share",attrs:{src:n("579c")}})]):t._e()]),i("mt-popup",{staticClass:"copy-wrapper",model:{value:t.copyVisible,callback:function(e){t.copyVisible=e},expression:"copyVisible"}},[i("div",{staticClass:"title"},[t._v("您的浏览器不支持直接复制，请手动复制")]),i("input",{attrs:{type:"text",onfocus:"this.select()"},domProps:{value:t.copyLink}})])],1)},o=[],r=(n("8e6e"),n("ac6a"),n("456d"),n("b54a"),n("5fc6"),n("5af2")),a=n.n(r),c=(n("7f7f"),n("bd86")),s=n("2934"),u=n("21f4"),l=n("3f6a"),d=n("2f62");function f(t,e){var n=Object.keys(t);if(Object.getOwnPropertySymbols){var i=Object.getOwnPropertySymbols(t);e&&(i=i.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),n.push.apply(n,i)}return n}function p(t){for(var e=1;e<arguments.length;e++){var n=null!=arguments[e]?arguments[e]:{};e%2?f(n,!0).forEach((function(e){Object(c["a"])(t,e,n[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(n)):f(n).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(n,e))}))}return t}var h={name:"HeaderMore",data:function(){return{shareVisible:!1,weixinShareImage:!1,copyVisible:!1,copyLink:window.location.host+this.$route.fullPath}},components:{},created:function(){},props:{goods_id:{},share_info:{},showDot:{}},computed:p({},Object(d["d"])({config:function(t){return t.config.config}}),{routerName:function(){return this.$route.name},getWrapperStyle:function(){var t=window.screen,e=t.width,n=t.height;return{width:e+"px",height:n+"px"}}}),watch:{share_info:function(t){t&&Object(l["a"])()&&this.config.wechat_appid&&this.setWechat(t)}},methods:{showLink:function(){window.clipboardData?(window.clipboardData.clearData(),window.clipboardData.setData("Text",this.copyLink),a()("复制成功！")):this.copyVisible=!0},onShare:function(){this.shareVisible=!0},setWechat:function(t){Object(s["c"])(encodeURIComponent(window.location.href)).then((function(e){Object(u["a"])("weixin","https://res.wx.qq.com/open/js/jweixin-1.3.2.js",(function(){wx.config({debug:!1,appId:e.result.signPackage.appId,timestamp:e.result.signPackage.timestamp,nonceStr:e.result.signPackage.nonceStr,signature:e.result.signPackage.signature,jsApiList:["onMenuShareTimeline","onMenuShareAppMessage","onMenuShareQQ","onMenuShareWeibo","onMenuShareQZone"]}),wx.ready((function(){wx.onMenuShareAppMessage({title:t.title,link:t.link,imgUrl:t.imgUrl,desc:t.desc}),wx.onMenuShareTimeline({title:t.title,link:t.link,imgUrl:t.imgUrl,desc:t.desc}),wx.onMenuShareQQ({title:t.title,desc:t.desc,link:t.link,imgUrl:t.imgUrl}),wx.onMenuShareWeibo({title:t.title,desc:t.desc,link:t.link,imgUrl:t.imgUrl}),wx.onMenuShareQZone({title:t.title,desc:t.desc,link:t.link,imgUrl:t.imgUrl})})),wx.error((function(t){}))}))})).catch((function(t){a()(t.message)}))}}},b=h,m=(n("866e"),n("2877")),g=Object(m["a"])(b,i,o,!1,null,"5aa50681",null);e["a"]=g.exports},"9f9e":function(t,e,n){"use strict";var i=n("a1e2"),o=n.n(i);o.a},a1e2:function(t,e,n){},b54a:function(t,e,n){"use strict";n("386b")("link",(function(t){return function(e){return t(this,"a","href",e)}}))},cb7e:function(t,e,n){},e661:function(t,e,n){},f2608:function(t,e,n){"use strict";var i=n("7f0a"),o=n.n(i);o.a}}]);