(window["webpackJsonp"]=window["webpackJsonp"]||[]).push([["chunk-592a2572"],{"1bb8":function(t,s,o){"use strict";var i=o("e941"),e=o.n(i);e.a},"1f42":function(t,s,o){"use strict";o.r(s);var i=function(){var t=this,s=t.$createElement,o=t._self._c||s;return o("div",{staticClass:"product-info",class:{border:t.showRightBorder},on:{click:t.productClick}},[o("img",{staticClass:"product-icon",attrs:{src:t.item.goods_image_url}}),o("span",{staticClass:"product-title"},[t._v(t._s(t.item.goods_name))]),o("div",{staticClass:"product-bottom"},[o("span",{staticClass:"product-price"},[t._v("￥"+t._s(t.item.goods_price))]),o("span",{staticClass:"product-buy"},[t._v(t._s(t.item.goods_salenum)+"人已购买")])])])},e=[],c={name:"StoreProductBody",data:function(){return{itemWidth:0,itemHeight:0,showRightBorder:this.index%2===0}},props:["item","index"],computed:{},methods:{productClick:function(){this.$router.push({name:"HomeGoodsdetail",query:{goods_id:this.item.goods_id}})}}},r=c,a=(o("1bb8"),o("2877")),n=Object(a["a"])(r,i,e,!1,null,"9276c76e",null);s["default"]=n.exports},e941:function(t,s,o){}}]);