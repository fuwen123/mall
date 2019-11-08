<template>
  <div class="editable-page-model2" :style="{width:editablePageConfig.editable_page_config_content.width,backgroundColor:editablePageConfig.editable_page_config_content.back_color,paddingTop:editablePageConfig.editable_page_config_content.margin_top,paddingBottom:editablePageConfig.editable_page_config_content.margin_bottom}">
    <div class="html" v-html="editablePageConfig.editable_page_config_content.editor[0]"></div>
  </div>
</template>

<script>
export default {
  name: 'Model2',
  data () {
    return {
    }
  },
  props: ['editablePageConfig'],
  computed: {

  },
  methods: {

  }
}
</script>

<style lang="scss">
.editable-page-model2 .html *{max-width:100%}
</style>
