(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-5649f057","chunk-592a2572"],{"0164":function(t,e,s){"use strict";var i=s("55b3"),o=s.n(i);o.a},"1bb8":function(t,e,s){"use strict";var i=s("e941"),o=s.n(i);o.a},"1f42":function(t,e,s){"use strict";s.r(e);var i=function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticClass:"product-info",class:{border:t.showRightBorder},on:{click:t.productClick}},[s("img",{staticClass:"product-icon",attrs:{src:t.item.goods_image_url}}),s("span",{staticClass:"product-title"},[t._v(t._s(t.item.goods_name))]),s("div",{staticClass:"product-bottom"},[s("span",{staticClass:"product-price"},[t._v("￥"+t._s(t.item.goods_price))]),s("span",{staticClass:"product-buy"},[t._v(t._s(t.item.goods_salenum)+"人已购买")])])])},o=[],r={name:"StoreProductBody",data:function(){return{itemWidth:0,itemHeight:0,showRightBorder:this.index%2===0}},props:["item","index"],computed:{},methods:{productClick:function(){this.$router.push({name:"HomeGoodsdetail",query:{goods_id:this.item.goods_id}})}}},c=r,n=(s("1bb8"),s("2877")),a=Object(n["a"])(c,i,o,!1,null,"9276c76e",null);e["default"]=a.exports},"2a60":function(t,e,s){"use strict";s.r(e);var i=function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticClass:"product-list"},[t._m(0),s("div",{staticClass:"product-list-body"},t._l(t.items,(function(t,e){return s("store-product-body",{key:e,attrs:{item:t,index:e}})})),1)])},o=[function(){var t=this,e=t.$createElement,s=t._self._c||e;return s("div",{staticClass:"product-list-header"},[s("span",{staticClass:"header-title"},[t._v("店铺推荐")])])}],r=s("1f42"),c={name:"StoreProductList",data:function(){return{}},props:["items","title","type"],components:{StoreProductBody:r["default"]},computed:{},methods:{productListClick:function(){this.$router.push({name:"StoreGoodslist",query:{sort_key:this.type}})}}},n=c,a=(s("0164"),s("2877")),u=Object(a["a"])(n,i,o,!1,null,"341ae8ee",null);e["default"]=u.exports},"55b3":function(t,e,s){},e941:function(t,e,s){}}]);