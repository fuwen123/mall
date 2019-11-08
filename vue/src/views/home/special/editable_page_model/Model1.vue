<template>
  <div :style="{backgroundColor:editablePageConfig.editable_page_config_content.back_color,paddingTop:editablePageConfig.editable_page_config_content.margin_top,paddingBottom:editablePageConfig.editable_page_config_content.margin_bottom}">
  <mt-swipe class="swipper-wrapper" v-bind:style="getBannerStyle" :showIndicators="isShowIndicators">
    <mt-swipe-item v-for="index in editablePageConfig.editable_page_config_content.image[0].count" :key="index">
      <img :style="getBannerStyle" v-lazy="editablePageConfig.editable_page_config_content.image[0].list[index-1].path" v-if='editablePageConfig.editable_page_config_content.image[0].list[index-1]' @click="goAdUrl(editablePageConfig.editable_page_config_content.link[0].list[index-1].content)">
      <img :style="getBannerStyle" src="../../../../assets/image/home/default_image_banner.png" v-else>
    </mt-swipe-item>
  </mt-swipe>
  </div>
</template>

<script>
export default {
  name: 'Model1',
  data () {
    return {
    }
  },
  props: ['editablePageConfig'],
  computed: {
    getBannerStyle: function () {
      const { width } = window.screen
      let itemWidth = width
      let itemHeight = this.editablePageConfig.editable_page_config_content.height
      return {
        width: itemWidth + 'px',
        height: itemHeight
      }
    },
    isShowIndicators () {
      if (this.editablePageConfig.editable_page_config_content.image[0].count) {
        return true
      }
      return false
    }
  },
  methods: {
    goAdUrl (url) {
      window.location.href = url
    }
  }
}
</script>

<style scoped>
.swipper-wrapper{margin: 0 auto}
</style>
