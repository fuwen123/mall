<template>
  <div class="editable-page-model12" :style="{backgroundColor:editablePageConfig.editable_page_config_content.back_color,paddingTop:editablePageConfig.editable_page_config_content.margin_top,paddingBottom:editablePageConfig.editable_page_config_content.margin_bottom}">
    <div class="menu clear-fix">
      <div class="menu-item"  v-for="index in editablePageConfig.editable_page_config_content.image[0].count" :key="index">
        <div v-if='editablePageConfig.editable_page_config_content.image[0].list[index-1]' @click="goAdUrl(editablePageConfig.editable_page_config_content.link[0].list[index-1]?editablePageConfig.editable_page_config_content.link[0].list[index-1].content:'')">
        <img class="menu-item-icon" :src="editablePageConfig.editable_page_config_content.image[0].list[index-1].path" />
        <span class="menu-item-title">{{editablePageConfig.editable_page_config_content.text[0].list[index-1]?editablePageConfig.editable_page_config_content.text[0].list[index-1].content:''}}</span>
        </div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Model12',
  data () {
    return {
    }
  },
  props: ['editablePageConfig'],
  computed: {

  },
  methods: {
    goAdUrl (url) {
      window.location.href = url
    }
  }
}
</script>

<style lang="scss" scoped>

  .menu {
    overflow: hidden;
    justify-content: space-around;
    background: #FFFFFF;
    .menu-item {
      margin: 1rem auto;
      width:20%;
      text-align:center;
      float: left;
      .iconfont{
        margin:auto;
        width: 2.2rem;
        height: 2.2rem;
        line-height:2.2rem;
        font-size:1.4rem;
        color:#fff;
        background-image: linear-gradient(to bottom , #d69dcc,#e8198b );
        overflow: hidden;
        display:block;
        text-align: center;
        border-radius:50%;
      }
      .menu-item-icon {
        width: 2.2rem;
        height: 2.2rem;
        overflow: hidden;
      }
      .menu-item-title {
        display:block;
        color: #333;
        font-size:.6rem;
        margin-top:.2rem;
      }
    }
  }
</style>
