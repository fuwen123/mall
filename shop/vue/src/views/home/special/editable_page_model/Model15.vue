<template>
  <div class="editable-page-model15" :style="{backgroundColor:editablePageConfig.editable_page_config_content.back_color,paddingTop:editablePageConfig.editable_page_config_content.margin_top,paddingBottom:editablePageConfig.editable_page_config_content.margin_bottom}">
    <div class="product-list-body">
      <div
              v-for="(item, index) in editablePageConfig.goods_list[0]"
              v-bind:key="index"
              class="product-info"
              @click="productClick(item.goods_id)"
      >
        <i>{{index+1}}</i>
        <img class="product-icon" v-lazy="item.goods_image_url"/>
        <p class="product-title">{{ item.goods_name }}</p>
        <p class="product-price">￥{{ item.goods_promotion_price }}</p>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Model15',
  data () {
    return {
    }
  },
  props: ['editablePageConfig'],

  methods: {
    productClick(goods_id) {
      this.$router.push({ name: 'HomeGoodsdetail', query: { goods_id: goods_id } })
    }
  }
}
</script>

<style lang="scss" scoped>
  .editable-page-model15 .product-list-body{overflow-x: auto;padding:.4rem;background:rgb(254,244,243);white-space: nowrap}
  .editable-page-model15 .product-list-body .product-info{display: inline-block;vertical-align: top;margin:.1rem;width:5rem;background: #fff;position: relative;padding-bottom:.2rem;}
  .editable-page-model15 .product-list-body .product-info .product-icon{width:100%}
  .editable-page-model15 .product-list-body .product-info .product-title{white-space: normal;display: -webkit-box;line-height: 1.5;padding:0 .2rem;color:#000;overflow: hidden;text-overflow: ellipsis;-webkit-line-clamp: 2;-webkit-box-orient: vertical;word-break: break-all;font-size: .6rem;height: 1.8rem}
  .editable-page-model15 .product-list-body .product-info .product-price{padding: 0 .2rem;color:#F0250F;font-size: .6rem;}
  .editable-page-model15 .product-list-body .product-info i{position: absolute;top:0;left:0;width:1rem;height: 1.2rem;line-height: 1.2rem;text-align: center;background-size: 100%;background-image: url(../../../../assets/image/home/home-special-label-back.png);background-repeat: no-repeat;background-position: center 0;font-size:.6rem;}
  .editable-page-model15 .product-list-body .product-info:nth-child(1) i,.editable-page-model15 .product-list-body .product-info:nth-child(2) i,.editable-page-model15 .product-list-body .product-info:nth-child(3) i{background-position: center -1.2rem;color:#fff}
</style>
