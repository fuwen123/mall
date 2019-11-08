<template>
  <div class="editable-page-model14" :style="{backgroundColor:editablePageConfig.editable_page_config_content.back_color,paddingTop:editablePageConfig.editable_page_config_content.margin_top,paddingBottom:editablePageConfig.editable_page_config_content.margin_bottom}">
    <div v-if="editablePageConfig['editable_page_config_content']['image'][0]['list']">
      <div class="tab_head">

        <div v-for="index in editablePageConfig.editable_page_config_content.image[0].count" @click="showIndex=index" :key="index" class="tab_head_item" :class="{'on':showIndex==index}" :style="{backgroundImage:'url('+(editablePageConfig.editable_page_config_content.image[0].list[index-1]?editablePageConfig.editable_page_config_content.image[0].list[index-1].path:'')+')'}">
        </div>

      </div>
    </div>
    <div class="tab_body">
    <div class="tab_body_list">
      <div v-for="index in editablePageConfig.editable_page_config_content.image[0].count" :key="index">
      <div class="tab_body_item" v-if="editablePageConfig.editable_page_config_content.image[index]" v-show="showIndex==index">
      <div class='image' v-for="(item,index2) in editablePageConfig.editable_page_config_content.image[index].list" :key="index2">
        <img :src="item.path" @click="goAdUrl(editablePageConfig.editable_page_config_content.link[index].list[index2].content)">
      </div>
    </div>
    </div>
  </div>
  </div>
  </div>
</template>

<script>
export default {
  name: 'Model14',
  data () {
    return {
      showIndex:1
    }
  },
  props: ['editablePageConfig'],

  methods: {
    goAdUrl (url) {
      window.location.href = url
    }
  }
}
</script>

<style lang="scss" scoped>
  .editable-page-model14{}
  .editable-page-model14 .tab_head{display: flex;height: 2.5rem;background: #fff}
  .editable-page-model14 .tab_head .tab_head_item{flex:1;background-position:center 0 ;background-repeat: no-repeat;background-size: 100%}
  .editable-page-model14 .tab_head .tab_head_item.on{background-position:center -2.5rem;}
  .editable-page-model14 .tab_body_list {min-height: 30px;background: #fff}
  .editable-page-model14 .tab_body{position: relative}
  .editable-page-model14 .tab_body_list .tab_body_item{display: flex;padding:.25rem}
  .editable-page-model14 .tab_body_list .tab_body_item .image{flex:1;padding:.25rem;}
  .editable-page-model14 .tab_body_list .tab_body_item .image img{width: 100%;}
</style>
