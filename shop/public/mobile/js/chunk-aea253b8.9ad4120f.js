(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-aea253b8"],{3975:function(t,e,n){"use strict";n.r(e);var o=function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"container"},[n("div",{staticClass:"common-header-wrap"},[n("mt-header",{staticClass:"common-header",attrs:{title:"商品管理"}},[n("mt-button",{attrs:{slot:"left",icon:"back"},on:{click:function(e){return t.$router.go(-1)}},slot:"left"})],1)],1),n("div",{staticClass:"order-header"},[n("ul",t._l(t.orderNav,(function(e){return n("li",{key:e.id,staticClass:"item",class:{active:t.goods_type==e.id},on:{click:function(n){return t.setOrderNavActive(e.id)}}},[t._v("\r\n                "+t._s(e.name)+"\r\n            ")])})),0)]),n("div",{directives:[{name:"infinite-scroll",rawName:"v-infinite-scroll",value:t.loadMore,expression:"loadMore"}],staticClass:"mt-30",attrs:{"infinite-scroll-disabled":"loading","infinite-scroll-distance":"10"}},[t.goodsList.length>0?n("div",t._l(t.goodsList,(function(e,o){return n("div",{staticClass:"mb-10 goods-item"},[n("div",{staticClass:"goods-info"},[n("div",{staticClass:"p-img"},[n("img",{attrs:{src:e.goods_image}})]),n("div",{staticClass:"p-info"},[n("div",{staticClass:"name"},[t._v(t._s(e.goods_name))]),n("div",{staticClass:"price"},[t._v(t._s(e.goods_price))]),n("div",{staticClass:"stock"},[t._v("库存:"+t._s(e.goods_storage_sum))])])]),n("div",{staticClass:"goods-btn"},[0===e.goods_state?n("mt-button",{staticClass:"mr-10",attrs:{size:"small",type:"primary"},on:{click:function(n){return t.goods_show(e.goods_commonid)}}},[t._v("上架")]):t._e(),1===e.goods_state?n("mt-button",{staticClass:"mr-10",attrs:{size:"small",type:"primary"},on:{click:function(n){return t.goods_unshow(e.goods_commonid)}}},[t._v("下架")]):t._e(),n("mt-button",{staticClass:"mr-10",attrs:{size:"small",type:"danger"},on:{click:function(n){return t.drop_goods(e.goods_commonid)}}},[t._v("删除")])],1)])})),0):n("empty-record")],1)])},s=[],i=(n("5fc6"),n("5af2")),r=n.n(i),a=(n("b6ce"),n("5441")),c=n.n(a),l=(n("ceaa"),n("b2fb")),u=n.n(l),d=n("ee53"),p=n("366f"),f=function(t,e,n,o){return Object(p["a"])("/Sellergoods/goods_list","POST",{keyword:e,goods_type:n,search_type:o,page:t.page,per_page:t.per_page,client_type:"wap"},"seller")},m=function(t){return Object(p["a"])("/Sellergoods/drop_goods","POST",{commonid:t,client_type:"wap"},"seller")},v=function(t){return Object(p["a"])("/Sellergoods/goods_show","POST",{commonid:t,client_type:"wap"},"seller")},_=function(t){return Object(p["a"])("/Sellergoods/goods_unshow","POST",{commonid:t,client_type:"wap"},"seller")},g={name:"Goodsonline",data:function(){return{params:{page:0,per_page:10},loading:!1,isMore:!0,pageTotal:1,goodsList:[],keyword:"",goods_type:"",search_type:"",orderNav:[{name:"出售中",id:""},{name:"仓库中",id:"offline"},{name:"待审核",id:"waitverify"},{name:"违规商品",id:"lockup"}]}},components:{EmptyRecord:d["a"]},created:function(){},methods:{setOrderNavActive:function(t){this.goods_type=t,this.reload()},getGoodsList:function(t){var e=this;u.a.open();var n=this.params;f(n,this.keyword,this.goods_type,this.search_type).then((function(n){u.a.close(),e.goodsList=t?e.goodsList.concat(n.result.goods_list):n.result.goods_list,e.pageTotal=n.result.page_total,n.result.hasmore?e.isMore=!0:e.isMore=!1}))},loadMore:function(){this.loading=!0,this.params.page=++this.params.page,this.isMore&&this.params.page<=this.pageTotal&&(this.loading=!1,this.getGoodsList(!0))},goods_show:function(t){var e=this;c.a.confirm("您确定要上架此商品吗？").then((function(n){u.a.open(),v(t).then((function(t){u.a.close(),e.reload()})).catch((function(t){r()(t.message)}))}))},goods_unshow:function(t){var e=this;c.a.confirm("您确定要下架此商品吗？").then((function(n){u.a.open(),_(t).then((function(t){u.a.close(),e.reload()})).catch((function(t){r()(t.message)}))}))},drop_goods:function(t){var e=this;c.a.confirm("您确定要下架此商品吗？").then((function(n){u.a.open(),m(t).then((function(t){u.a.close(),e.reload()})).catch((function(t){r()(t.message)}))}))},reload:function(){this.params.page=0,this.isMore=!0,this.goodsList=[],this.loadMore()}}},h=g,b=(n("8798"),n("2877")),y=Object(b["a"])(h,o,s,!1,null,"7d2623ba",null);e["default"]=y.exports},"5af2":function(t,e,n){t.exports=function(t){var e={};function n(o){if(e[o])return e[o].exports;var s=e[o]={i:o,l:!1,exports:{}};return t[o].call(s.exports,s,s.exports,n),s.l=!0,s.exports}return n.m=t,n.c=e,n.i=function(t){return t},n.d=function(t,e,o){n.o(t,e)||Object.defineProperty(t,e,{configurable:!1,enumerable:!0,get:o})},n.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=242)}({0:function(t,e){t.exports=function(t,e,n,o,s){var i,r=t=t||{},a=typeof t.default;"object"!==a&&"function"!==a||(i=t,r=t.default);var c,l="function"===typeof r?r.options:r;if(e&&(l.render=e.render,l.staticRenderFns=e.staticRenderFns),o&&(l._scopeId=o),s?(c=function(t){t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,t||"undefined"===typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),n&&n.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(s)},l._ssrRegister=c):n&&(c=n),c){var u=l.functional,d=u?l.render:l.beforeCreate;u?l.render=function(t,e){return c.call(e),d(t,e)}:l.beforeCreate=d?[].concat(d,c):[c]}return{esModule:i,exports:r,options:l}}},1:function(t,e){t.exports=n("2b0e")},101:function(t,e){},164:function(t,e,n){function o(t){n(101)}var s=n(0)(n(86),n(170),o,null,null);t.exports=s.exports},170:function(t,e){t.exports={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("transition",{attrs:{name:"mint-toast-pop"}},[n("div",{directives:[{name:"show",rawName:"v-show",value:t.visible,expression:"visible"}],staticClass:"mint-toast",class:t.customClass,style:{padding:""===t.iconClass?"10px":"20px"}},[""!==t.iconClass?n("i",{staticClass:"mint-toast-icon",class:t.iconClass}):t._e(),t._v(" "),n("span",{staticClass:"mint-toast-text",style:{"padding-top":""===t.iconClass?"0":"10px"}},[t._v(t._s(t.message))])])])},staticRenderFns:[]}},242:function(t,e,n){t.exports=n(50)},50:function(t,e,n){"use strict";var o=n(94);Object.defineProperty(e,"__esModule",{value:!0}),n.d(e,"default",(function(){return o["a"]}))},86:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0}),e["default"]={props:{message:String,className:{type:String,default:""},position:{type:String,default:"middle"},iconClass:{type:String,default:""}},data:function(){return{visible:!1}},computed:{customClass:function(){var t=[];switch(this.position){case"top":t.push("is-placetop");break;case"bottom":t.push("is-placebottom");break;default:t.push("is-placemiddle")}return t.push(this.className),t.join(" ")}}}},94:function(t,e,n){"use strict";var o=n(1),s=n.n(o),i=s.a.extend(n(164)),r=[],a=function(){if(r.length>0){var t=r[0];return r.splice(0,1),t}return new i({el:document.createElement("div")})},c=function(t){t&&r.push(t)},l=function(t){t.target.parentNode&&t.target.parentNode.removeChild(t.target)};i.prototype.close=function(){this.visible=!1,this.$el.addEventListener("transitionend",l),this.closed=!0,c(this)};var u=function(t){void 0===t&&(t={});var e=t.duration||3e3,n=a();return n.closed=!1,clearTimeout(n.timer),n.message="string"===typeof t?t:t.message,n.position=t.position||"middle",n.className=t.className||"",n.iconClass=t.iconClass||"",document.body.appendChild(n.$el),s.a.nextTick((function(){n.visible=!0,n.$el.removeEventListener("transitionend",l),~e&&(n.timer=setTimeout((function(){n.closed||n.close()}),e))})),n};e["a"]=u}})},"5fc6":function(t,e,n){},"7cff":function(t,e,n){},8798:function(t,e,n){"use strict";var o=n("7cff"),s=n.n(o);s.a},b2fb:function(t,e,n){t.exports=function(t){var e={};function n(o){if(e[o])return e[o].exports;var s=e[o]={i:o,l:!1,exports:{}};return t[o].call(s.exports,s,s.exports,n),s.l=!0,s.exports}return n.m=t,n.c=e,n.i=function(t){return t},n.d=function(t,e,o){n.o(t,e)||Object.defineProperty(t,e,{configurable:!1,enumerable:!0,get:o})},n.n=function(t){var e=t&&t.__esModule?function(){return t["default"]}:function(){return t};return n.d(e,"a",e),e},n.o=function(t,e){return Object.prototype.hasOwnProperty.call(t,e)},n.p="",n(n.s=221)}({0:function(t,e){t.exports=function(t,e,n,o,s){var i,r=t=t||{},a=typeof t.default;"object"!==a&&"function"!==a||(i=t,r=t.default);var c,l="function"===typeof r?r.options:r;if(e&&(l.render=e.render,l.staticRenderFns=e.staticRenderFns),o&&(l._scopeId=o),s?(c=function(t){t=t||this.$vnode&&this.$vnode.ssrContext||this.parent&&this.parent.$vnode&&this.parent.$vnode.ssrContext,t||"undefined"===typeof __VUE_SSR_CONTEXT__||(t=__VUE_SSR_CONTEXT__),n&&n.call(this,t),t&&t._registeredComponents&&t._registeredComponents.add(s)},l._ssrRegister=c):n&&(c=n),c){var u=l.functional,d=u?l.render:l.beforeCreate;u?l.render=function(t,e){return c.call(e),d(t,e)}:l.beforeCreate=d?[].concat(d,c):[c]}return{esModule:i,exports:r,options:l}}},1:function(t,e){t.exports=n("2b0e")},122:function(t,e){},141:function(t,e,n){function o(t){n(122)}var s=n(0)(n(63),n(192),o,null,null);t.exports=s.exports},192:function(t,e){t.exports={render:function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("transition",{attrs:{name:"mint-indicator"}},[n("div",{directives:[{name:"show",rawName:"v-show",value:t.visible,expression:"visible"}],staticClass:"mint-indicator"},[n("div",{staticClass:"mint-indicator-wrapper",style:{padding:t.text?"20px":"15px"}},[n("spinner",{staticClass:"mint-indicator-spin",attrs:{type:t.convertedSpinnerType,size:32}}),t._v(" "),n("span",{directives:[{name:"show",rawName:"v-show",value:t.text,expression:"text"}],staticClass:"mint-indicator-text"},[t._v(t._s(t.text))])],1),t._v(" "),n("div",{staticClass:"mint-indicator-mask",on:{touchmove:function(t){t.stopPropagation(),t.preventDefault()}}})])])},staticRenderFns:[]}},205:function(t,e){t.exports=n("e8c9")},206:function(t,e){t.exports=n("c6f8")},221:function(t,e,n){t.exports=n(29)},29:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var o,s=n(1),i=n.n(s),r=i.a.extend(n(141));e["default"]={open:function(t){void 0===t&&(t={}),o||(o=new r({el:document.createElement("div")})),o.visible||(o.text="string"===typeof t?t:t.text||"",o.spinnerType=t.spinnerType||"snake",document.body.appendChild(o.$el),i.a.nextTick((function(){o.visible=!0})))},close:function(){o&&(o.visible=!1)}}},63:function(t,e,n){"use strict";Object.defineProperty(e,"__esModule",{value:!0});var o=n(205),s=n.n(o);n(206),e["default"]={data:function(){return{visible:!1}},components:{Spinner:s.a},computed:{convertedSpinnerType:function(){switch(this.spinnerType){case"double-bounce":return 1;case"triple-bounce":return 2;case"fading-circle":return 3;default:return 0}}},props:{text:String,spinnerType:{type:String,default:"snake"}}}}})},ceaa:function(t,e,n){},ee53:function(t,e,n){"use strict";var o=function(){var t=this,e=t.$createElement;t._self._c;return t._m(0)},s=[function(){var t=this,e=t.$createElement,n=t._self._c||e;return n("div",{staticClass:"common-empty-record"},[n("i",{staticClass:"iconfont"},[t._v("")]),n("p",[t._v("没有找到相关记录")])])}],i={name:"EmptyRecord",data:function(){return{}},props:{},methods:{}},r=i,a=n("2877"),c=Object(a["a"])(r,o,s,!1,null,null,null);e["a"]=c.exports}}]);