(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-db0b9666","chunk-5649f057","chunk-592a2572","chunk-2d230fb4"],{"0164":function(t,e,i){"use strict";var n=i("55b3"),s=i.n(n);s.a},"1bb8":function(t,e,i){"use strict";var n=i("e941"),s=i.n(n);s.a},"1f42":function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"product-info",class:{border:t.showRightBorder},on:{click:t.productClick}},[i("img",{staticClass:"product-icon",attrs:{src:t.item.goods_image_url}}),i("span",{staticClass:"product-title"},[t._v(t._s(t.item.goods_name))]),i("div",{staticClass:"product-bottom"},[i("span",{staticClass:"product-price"},[t._v("￥"+t._s(t.item.goods_price))]),i("span",{staticClass:"product-buy"},[t._v(t._s(t.item.goods_salenum)+"人已购买")])])])},s=[],o={name:"StoreProductBody",data:function(){return{itemWidth:0,itemHeight:0,showRightBorder:this.index%2===0}},props:["item","index"],computed:{},methods:{productClick:function(){this.$router.push({name:"HomeGoodsdetail",query:{goods_id:this.item.goods_id}})}}},r=o,a=(i("1bb8"),i("2877")),c=Object(a["a"])(r,n,s,!1,null,"9276c76e",null);e["default"]=c.exports},2934:function(t,e,i){"use strict";i.d(e,"b",(function(){return s})),i.d(e,"a",(function(){return o})),i.d(e,"c",(function(){return r}));var n=i("366f"),s=function(t,e){return Object(n["a"])("/Connect/get_sms_captcha","GET",{type:t,phone:e})},o=function(t){return Object(n["a"])("/Seccode/check","POST",{captcha:t})},r=function(t){return Object(n["a"])("/index/getWechatShare","POST",{url:t})}},"2a60":function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"product-list"},[t._m(0),i("div",{staticClass:"product-list-body"},t._l(t.items,(function(t,e){return i("store-product-body",{key:e,attrs:{item:t,index:e}})})),1)])},s=[function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"product-list-header"},[i("span",{staticClass:"header-title"},[t._v("店铺推荐")])])}],o=i("1f42"),r={name:"StoreProductList",data:function(){return{}},props:["items","title","type"],components:{StoreProductBody:o["default"]},computed:{},methods:{productListClick:function(){this.$router.push({name:"StoreGoodslist",query:{sort_key:this.type}})}}},a=r,c=(i("0164"),i("2877")),u=Object(c["a"])(a,n,s,!1,null,"341ae8ee",null);e["default"]=u.exports},"386b":function(t,e,i){var n=i("5ca1"),s=i("79e5"),o=i("be13"),r=/"/g,a=function(t,e,i,n){var s=String(o(t)),a="<"+e;return""!==i&&(a+=" "+i+'="'+String(n).replace(r,"&quot;")+'"'),a+">"+s+"</"+e+">"};t.exports=function(t,e){var i={};i[t]=e(a),n(n.P+n.F*s((function(){var e=""[t]('"');return e!==e.toLowerCase()||e.split('"').length>3})),"String",i)}},"3f6a":function(t,e,i){"use strict";i.d(e,"a",(function(){return n}));i("4917");function n(){var t=window.navigator.userAgent.toLowerCase();return"micromessenger"===t.match(/MicroMessenger/i)}},4917:function(t,e,i){"use strict";var n=i("cb7c"),s=i("9def"),o=i("0390"),r=i("5f1b");i("214f")("match",1,(function(t,e,i,a){return[function(i){var n=t(this),s=void 0==i?void 0:i[e];return void 0!==s?s.call(i,n):new RegExp(i)[e](String(n))},function(t){var e=a(i,t,this);if(e.done)return e.value;var c=n(t),u=String(this);if(!c.global)return r(c,u);var l=c.unicode;c.lastIndex=0;var d,f=[],m=0;while(null!==(d=r(c,u))){var p=String(d[0]);f[m]=p,""===p&&(c.lastIndex=o(u,s(c.lastIndex),l)),m++}return 0===m?null:f}]}))},"55b3":function(t,e,i){},"579c":function(t,e,i){t.exports=i.p+"img/weixin-share.019164ba.png"},"5af2":function(t,e,i){t.exports=function(t){var e={};function i(n){if(e[n])return e[n].exports;var s=e[n]={i:n,l:!1,exports:{}};return t[n].call(s.exports,s,s.exports,i),s.l=!0,s.exports}return i.m=t,i.c=e,i.i=function(t){return t},i.d=function(t,e,n){i.o(t,e)||Object.defineProperty(t,e,{configurable:!1,enumerable:!0,get:n})},i.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return i.d(e,"a",e),e},i.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},i.p="",i(i.s=242)}({0:function(t,e){t.exports=function(t,e,i,n,s){var o,r=t=t||{},a=typeof t.default;"object"!==a&&"function"!==a||(o=t,r=t.default);var c,u="function"===typeof r?r.options:r;if(e&&(u.render=e.render,u.staticRenderFns=e.staticRenderFns),n&&(u._scopeId=n),s?(c=function(t){t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,t||"undefined"===typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),i&&i.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(s)},u._ssrRegister=c):i&&(c=i),c){var l=u.functional,d=l?u.render:u.beforeCreate;l?u.render=function(t,e){return c.call(e),d(t,e)}:u.beforeCreate=d?[].concat(d,c):[c]}return{esModule:o,exports:r,options:u}}},1:function(t,e){t.exports=i("2b0e")},101:function(t,e){},164:function(t,e,i){function n(t){i(101)}var s=i(0)(i(86),i(170),n,null,null);t.exports=s.exports},170:function(t,e){t.exports={render:function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("transition",{attrs:{name:"mint-toast-pop"}},[i("div",{directives:[{name:"show",rawName:"v-show",value:t.visible,expression:"visible"}],staticClass:"mint-toast",class:t.customClass,style:{padding:""===t.iconClass?"10px":"20px"}},[""!==t.iconClass?i("i",{staticClass:"mint-toast-icon",class:t.iconClass}):t._e(),t._v(" "),i("span",{staticClass:"mint-toast-text",style:{"padding-top":""===t.iconClass?"0":"10px"}},[t._v(t._s(t.message))])])])},staticRenderFns:[]}},242:function(t,e,i){t.exports=i(50)},50:function(t,e,i){"use strict";var n=i(94);Object.defineProperty(e,"__esModule",{value:!0}),i.d(e,"default",(function(){return n["a"]}))},86:function(t,e,i){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e["default"]={props:{message:String,className:{type:String,default:""},position:{type:String,default:"middle"},iconClass:{type:String,default:""}},data:function(){return{visible:!1}},computed:{customClass:function(){var t=[];switch(this.position){case"top":t.push("is-placetop");break;case"bottom":t.push("is-placebottom");break;default:t.push("is-placemiddle")}return t.push(this.className),t.join(" ")}}}},94:function(t,e,i){"use strict";var n=i(1),s=i.n(n),o=s.a.extend(i(164)),r=[],a=function(){if(r.length>0){var t=r[0];return r.splice(0,1),t}return new o({el:document.createElement("div")})},c=function(t){t&&r.push(t)},u=function(t){t.target.parentNode&&t.target.parentNode.removeChild(t.target)};o.prototype.close=function(){this.visible=!1,this.$el.addEventListener("transitionend",u),this.closed=!0,c(this)};var l=function(t){void 0===t&&(t={});var e=t.duration||3e3,i=a();return i.closed=!1,clearTimeout(i.timer),i.message="string"===typeof t?t:t.message,i.position=t.position||"middle",i.className=t.className||"",i.iconClass=t.iconClass||"",document.body.appendChild(i.$el),s.a.nextTick((function(){i.visible=!0,i.$el.removeEventListener("transitionend",u),~e&&(i.timer=setTimeout((function(){i.closed||i.close()}),e))})),i};e["a"]=l}})},"5fc6":function(t,e,i){},"70fe":function(t,e,i){},"866e":function(t,e,i){"use strict";var n=i("e661"),s=i.n(n);s.a},"883f":function(t,e,i){"use strict";i.d(e,"d",(function(){return s})),i.d(e,"a",(function(){return o})),i.d(e,"c",(function(){return r})),i.d(e,"e",(function(){return a})),i.d(e,"b",(function(){return c}));var n=i("366f"),s=function(t,e){return Object(n["a"])("/Memberchat/get_node_info","POST",{u_id:t,chat_goods_id:e},"member")},o=function(t){return Object(n["a"])("/Memberchat/send_msg","POST",t,"member")},r=function(t,e){return Object(n["a"])("/Memberchat/get_chat_log","POST",{page:t.page,per_page:t.per_page,t_id:e},"member")},a=function(){return Object(n["a"])("/Memberchat/get_user_list","POST",{recent:1},"member")},c=function(){return Object(n["a"])("/Memberchat/get_msg_count","POST",{},"member")}},"9e98":function(t,e,i){"use strict";var n=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"more-box"},[n("div",{staticClass:"more-item",on:{click:function(e){return t.$router.push({name:"HomeIndex"})}}},[n("i",{staticClass:"iconfont"},[t._v("")]),t._v("首页")]),"HomeGoodsdetail"===t.routerName?n("div",{staticClass:"more-item",on:{click:function(e){return t.$router.push({name:"MemberInformForm",query:{goods_id:t.goods_id}})}}},[n("i",{staticClass:"iconfont"},[t._v("")]),t._v("违规举报")]):t._e(),n("div",{staticClass:"more-item",on:{click:function(e){return t.$router.push({name:"HomeSearch"})}}},[n("i",{staticClass:"iconfont"},[t._v("")]),t._v("搜索")]),n("div",{staticClass:"more-item",on:{click:function(e){return t.$router.push({name:"HomeCart"})}}},[n("i",{staticClass:"iconfont"},[t._v("")]),t._v("购物车")]),t.config&&"1"==t.config.node_site_use&&t.config.node_site_url?n("div",{staticClass:"more-item",on:{click:function(e){return t.$router.push({name:"MemberChatList"})}}},[n("i",{staticClass:"iconfont"},[t._v("")]),t._v("聊天消息"),t.showDot&&t.showDot.chat?n("div",{staticClass:"dot"}):t._e()]):t._e(),n("div",{staticClass:"more-item",on:{click:function(e){return t.$router.push({name:"MemberIndex"})}}},[n("i",{staticClass:"iconfont"},[t._v("")]),t._v("我的商城")]),n("div",{staticClass:"more-item",on:{click:t.onShare}},[n("i",{staticClass:"iconfont"},[t._v("")]),t._v("分享")]),n("i",{staticClass:"arrow"}),n("mt-popup",{staticClass:"common-popup-wrapper",attrs:{position:"bottom"},model:{value:t.shareVisible,callback:function(e){t.shareVisible=e},expression:"shareVisible"}},[n("div",{staticClass:"common-header-wrap"},[n("mt-header",{staticClass:"common-header",attrs:{title:"分享至"}},[n("mt-button",{attrs:{slot:"left",icon:"back"},on:{click:function(e){t.shareVisible=!1}},slot:"left"})],1)],1),n("div",{staticClass:"common-popup-content"},[n("div",{staticClass:"share-list"},[n("div",{staticClass:"share-item weixin",on:{click:function(e){t.weixinShareImage=!0}}},[t._v("微信")]),n("div",{staticClass:"share-item copy",on:{click:t.showLink}},[t._v("复制")])])]),t.weixinShareImage?n("div",{staticClass:"weixin-share-wrapper",style:t.getWrapperStyle,on:{click:function(e){t.weixinShareImage=!1}}},[n("img",{staticClass:"weixin-share",attrs:{src:i("579c")}})]):t._e()]),n("mt-popup",{staticClass:"copy-wrapper",model:{value:t.copyVisible,callback:function(e){t.copyVisible=e},expression:"copyVisible"}},[n("div",{staticClass:"title"},[t._v("您的浏览器不支持直接复制，请手动复制")]),n("input",{attrs:{type:"text",onfocus:"this.select()"},domProps:{value:t.copyLink}})])],1)},s=[],o=(i("8e6e"),i("ac6a"),i("456d"),i("b54a"),i("5fc6"),i("5af2")),r=i.n(o),a=(i("7f7f"),i("bd86")),c=i("2934"),u=i("21f4"),l=i("3f6a"),d=i("2f62");function f(t,e){var i=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),i.push.apply(i,n)}return i}function m(t){for(var e=1;e<arguments.length;e++){var i=null!=arguments[e]?arguments[e]:{};e%2?f(i,!0).forEach((function(e){Object(a["a"])(t,e,i[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(i)):f(i).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(i,e))}))}return t}var p={name:"HeaderMore",data:function(){return{shareVisible:!1,weixinShareImage:!1,copyVisible:!1,copyLink:window.location.host+this.$route.fullPath}},components:{},created:function(){},props:{goods_id:{},share_info:{},showDot:{}},computed:m({},Object(d["d"])({config:function(t){return t.config.config}}),{routerName:function(){return this.$route.name},getWrapperStyle:function(){var t=window.screen,e=t.width,i=t.height;return{width:e+"px",height:i+"px"}}}),watch:{share_info:function(t){t&&Object(l["a"])()&&this.config.wechat_appid&&this.setWechat(t)}},methods:{showLink:function(){window.clipboardData?(window.clipboardData.clearData(),window.clipboardData.setData("Text",this.copyLink),r()("复制成功！")):this.copyVisible=!0},onShare:function(){this.shareVisible=!0},setWechat:function(t){Object(c["c"])(encodeURIComponent(window.location.href)).then((function(e){Object(u["a"])("weixin","https://res.wx.qq.com/open/js/jweixin-1.3.2.js",(function(){wx.config({debug:!1,appId:e.result.signPackage.appId,timestamp:e.result.signPackage.timestamp,nonceStr:e.result.signPackage.nonceStr,signature:e.result.signPackage.signature,jsApiList:["onMenuShareTimeline","onMenuShareAppMessage","onMenuShareQQ","onMenuShareWeibo","onMenuShareQZone"]}),wx.ready((function(){wx.onMenuShareAppMessage({title:t.title,link:t.link,imgUrl:t.imgUrl,desc:t.desc}),wx.onMenuShareTimeline({title:t.title,link:t.link,imgUrl:t.imgUrl,desc:t.desc}),wx.onMenuShareQQ({title:t.title,desc:t.desc,link:t.link,imgUrl:t.imgUrl}),wx.onMenuShareWeibo({title:t.title,desc:t.desc,link:t.link,imgUrl:t.imgUrl}),wx.onMenuShareQZone({title:t.title,desc:t.desc,link:t.link,imgUrl:t.imgUrl})})),wx.error((function(t){}))}))})).catch((function(t){r()(t.message)}))}}},h=p,_=(i("866e"),i("2877")),b=Object(_["a"])(h,n,s,!1,null,"5aa50681",null);e["a"]=b.exports},"9f67":function(t,e,i){"use strict";i.d(e,"e",(function(){return s})),i.d(e,"a",(function(){return o})),i.d(e,"c",(function(){return r})),i.d(e,"d",(function(){return a})),i.d(e,"b",(function(){return c}));var n=i("366f"),s=function(t){return Object(n["a"])("/Memberfavoritesstore/favorites_list","POST",{},"member")},o=function(t){return Object(n["a"])("/Memberfavoritesstore/favorites_add","POST",{store_id:t},"member")},r=function(t){return Object(n["a"])("/Memberfavoritesstore/favorites_del","POST",{fav_id:t},"member")},a=function(t){return Object(n["a"])("/Memberfavorites/favorites_list","POST",{page:t},"member")},c=function(t){return Object(n["a"])("/Memberfavorites/favorites_del","POST",{fav_id:t},"member")}},b222:function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"container"},[i("div",{staticClass:"header",style:{backgroundImage:"url("+t.store.mb_title_img+")"}},[i("div",{staticClass:"content-wrapper"},[i("div",{staticClass:"avatar",on:{click:t.goStoreabout}},[i("img",{attrs:{src:t.store.store_avatar}})]),i("div",{staticClass:"content"},[i("div",{staticClass:"store_name"},[t._v(t._s(t.store.store_name))]),t.store.is_platform_store&&t.config.business_licence||t.store.business_licence_number_electronic?i("div",{on:{click:function(e){t.imageVisible=!0}}},[i("i",{staticClass:"iconfont"},[t._v("")])]):t._e()]),i("div",{staticClass:"follow_panel"},[i("div",{staticClass:"follow_button",on:{click:t.toggleFavorite}},[i("i",{staticClass:"iconfont",class:{active:t.store.is_favorate},domProps:{innerHTML:t._s(t.favoriteIco)}}),t._v("\n          "+t._s(t.favoriteName)+"\n        ")]),i("div",{staticClass:"follow_number"},[t._v("\n          "+t._s(t.store.store_collect)+"人收藏\n        ")])]),i("i",{staticClass:"header-more iconfont",on:{click:t.popupMore}},[t._v(""),t.showDot?i("div",{staticClass:"dot"}):t._e()])]),i("div",{staticClass:"background"},[(t.store.store_logo,i("img",{directives:[{name:"lazy",rawName:"v-lazy",value:t.store.store_logo,expression:"store.store_logo"}],attrs:{width:"100%",height:"100%"}}))])]),i("header-more",{directives:[{name:"show",rawName:"v-show",value:t.popupVisible,expression:"popupVisible"}],attrs:{showDot:t.showDot}}),t.store.mb_sliders&&t.store.mb_sliders.length?i("mt-swipe",{staticClass:"mt-5",style:t.getBannerStyle,attrs:{showIndicators:t.isShowIndicators}},t._l(t.store.mb_sliders,(function(e,n){return i("mt-swipe-item",{key:n},[i("img",{directives:[{name:"lazy",rawName:"v-lazy",value:e.imgUrl,expression:"item.imgUrl"}],style:t.getBannerStyle,on:{click:function(i){return t.goAd(e)}}})])})),1):t._e(),t.rec_goods_list&&t.rec_goods_list.length>0?i("store-product-list",{attrs:{items:t.rec_goods_list}}):t._e(),i("common-store-footer",{attrs:{store_id:t.store.store_id}}),i("mt-popup",{staticClass:"middle-popup",attrs:{"popup-transition":"popup-fade"},model:{value:t.imageVisible,callback:function(e){t.imageVisible=e},expression:"imageVisible"}},[t.store.business_licence_number_electronic?i("img",{attrs:{src:t.store.business_licence_number_electronic}}):t._e(),t.store.is_platform_store&&t.config.business_licence?i("img",{attrs:{src:t.config.business_licence}}):t._e()])],1)},s=[],o=(i("8e6e"),i("ac6a"),i("456d"),i("b54a"),i("5fc6"),i("5af2")),r=i.n(o),a=i("bd86"),c=i("2f62"),u=i("ef30"),l=i("e283"),d=i("883f"),f=i("9f67"),m=i("2a60"),p=i("9e98");function h(t,e){var i=Object.keys(t);if(Object.getOwnPropertySymbols){var n=Object.getOwnPropertySymbols(t);e&&(n=n.filter((function(e){return Object.getOwnPropertyDescriptor(t,e).enumerable}))),i.push.apply(i,n)}return i}function _(t){for(var e=1;e<arguments.length;e++){var i=null!=arguments[e]?arguments[e]:{};e%2?h(i,!0).forEach((function(e){Object(a["a"])(t,e,i[e])})):Object.getOwnPropertyDescriptors?Object.defineProperties(t,Object.getOwnPropertyDescriptors(i)):h(i).forEach((function(e){Object.defineProperty(t,e,Object.getOwnPropertyDescriptor(i,e))}))}return t}var b={name:"Storedetail",data:function(){return{imageVisible:!1,store:{store_id:this.$route.query.id?this.$route.query.id:""},rec_goods_list:!1,popupVisible:!1,showDot:!1}},components:{HeaderMore:p["a"],StoreProductList:m["default"],CommonStoreFooter:u["default"]},computed:_({},Object(c["d"])({config:function(t){return t.config.config},token:function(t){return t.member.token},isOnline:function(t){return t.member.isOnline}}),{getBannerStyle:function(){var t=window.screen.width,e=t,i=.5*t;return{width:e+"px",height:i+"px"}},isShowIndicators:function(){return!!(this.store.mb_sliders&&this.store.mb_sliders.length>1)},favoriteIco:function(){return this.store.is_favorate?"&#xe64d;":"&#xe64e;"},favoriteName:function(){return this.store.is_favorate?"已收藏":"收藏"}}),created:function(){var t=this;Object(l["c"])(this.store.store_id,this.token).then((function(e){t.store=e.result.store_info,t.rec_goods_list=e.result.rec_goods_list,t.store.is_platform_store&&t.fetchConfig({}).then((function(t){}),(function(t){r()(t.message)}))})).catch((function(t){})),this.isOnline&&Object(d["b"])().then((function(e){e.result&&(t.showDot["chat"]=!0)}))},methods:_({},Object(c["b"])({fetchConfig:"fetchConfig"}),{goAd:function(t){2===t.type?this.$router.push({name:"HomeGoodsdetail",query:{goods_id:t.link}}):window.location.href=t.link},goStoreabout:function(){this.$router.push({name:"HomeStoreabout",query:{id:this.$route.query.id}})},popupMore:function(){this.popupVisible?this.popupVisible=!1:this.popupVisible=!0},toggleFavorite:function(){var t=this;this.store.is_favorate?Object(f["c"])(this.store.store_id).then((function(e){r()(e.message),t.store.is_favorate=!t.store.is_favorate}),(function(t){r()(t.message)})):Object(f["a"])(this.store.store_id).then((function(e){r()(e.message),t.store.is_favorate=!t.store.is_favorate}),(function(t){r()(t.message)}))}})},v=b,g=(i("dd53"),i("2877")),C=Object(g["a"])(v,n,s,!1,null,"82f0b182",null);e["default"]=C.exports},b54a:function(t,e,i){"use strict";i("386b")("link",(function(t){return function(e){return t(this,"a","href",e)}}))},dd53:function(t,e,i){"use strict";var n=i("70fe"),s=i.n(n);s.a},e283:function(t,e,i){"use strict";i.d(e,"d",(function(){return s})),i.d(e,"c",(function(){return o})),i.d(e,"a",(function(){return r})),i.d(e,"b",(function(){return a}));var n=i("366f"),s=function(t){return Object(n["a"])("/Membervoucher/voucher_point","POST",{tid:t},"member")},o=function(t,e){return Object(n["a"])("/Store/store_info","POST",{store_id:t,key:e})},r=function(t){return Object(n["a"])("/Store/store_goods_class","POST",{store_id:t})},a=function(t){return Object(n["a"])("/Store/store_goods","POST",{page:t.page,per_page:t.per_page,storegc_id:t.gc_id,keyword:t.keyword,store_id:t.store_id,sort_order:t.sort_order,sort_key:t.sort_key})}},e661:function(t,e,i){},e941:function(t,e,i){},ef30:function(t,e,i){"use strict";i.r(e);var n=function(){var t=this,e=t.$createElement,i=t._self._c||e;return i("div",{staticClass:"home-base"},[i("router-view"),i("div",{staticClass:"common-footer-wrap"},[i("mt-tabbar",{staticClass:"common-footer",attrs:{fixed:""},model:{value:t.home_selected,callback:function(e){t.home_selected=e},expression:"home_selected"}},[i("mt-tab-item",{staticClass:"item-wrap",class:{active:"HomeStoredetail"==t.$route.name},attrs:{id:"home_index"}},[i("router-link",{staticClass:"item",attrs:{to:{name:"HomeStoredetail",query:{id:t.store_id}}}},[i("i",{staticClass:"iconfont icon-homepage"}),i("span",{staticClass:"text"},[t._v("店铺首页")])])],1),i("mt-tab-item",{staticClass:"item-wrap",class:{active:"HomeStoreGoodslist"==t.$route.name},attrs:{id:"home_goodsclass"}},[i("router-link",{staticClass:"item",attrs:{to:{name:"HomeStoreGoodslist",query:{id:t.store_id}}}},[i("i",{staticClass:"iconfont icon-31quanbushangpin"}),i("span",{staticClass:"text"},[t._v("全部商品")])])],1),i("mt-tab-item",{staticClass:"item-wrap",class:{active:"HomeStoreGoodsclass"==t.$route.name},attrs:{id:"home_search"}},[i("router-link",{staticClass:"item",attrs:{to:{name:"HomeStoreGoodsclass",query:{id:t.store_id}}}},[i("i",{staticClass:"iconfont icon-sousuo1"}),i("span",{staticClass:"text"},[t._v("店内搜索")])])],1),i("mt-tab-item",{staticClass:"item-wrap",class:{active:"HomeCart"==t.$route.name},attrs:{id:"home_cart"}},[i("router-link",{staticClass:"item",attrs:{to:{name:"HomeCart"}}},[i("i",{staticClass:"iconfont icon-31gouwuche"}),i("span",{staticClass:"text"},[t._v("购物车")])])],1),i("mt-tab-item",{staticClass:"item-wrap",class:{active:"MemberIndex"==t.$route.name},attrs:{id:"member_index"}},[i("router-link",{staticClass:"item",attrs:{to:{name:"MemberIndex"}}},[i("i",{staticClass:"iconfont icon-people"}),i("span",{staticClass:"text"},[t._v("我的")])])],1)],1)],1)],1)},s=[],o={name:"CommonStoreFooter",data:function(){return{home_selected:"home_index"}},props:["store_id"],computed:{},created:function(){}},r=o,a=i("2877"),c=Object(a["a"])(r,n,s,!1,null,null,null);e["default"]=c.exports}}]);