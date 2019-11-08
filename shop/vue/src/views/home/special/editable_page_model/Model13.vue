<template>
  <div class="editable-page-model13" :style="{backgroundColor:editablePageConfig.editable_page_config_content.back_color,paddingTop:editablePageConfig.editable_page_config_content.margin_top,paddingBottom:editablePageConfig.editable_page_config_content.margin_bottom}">
    <div class="index-article">
      <em class="title"><img :src="editablePageConfig.editable_page_config_content.image[0].list[0]?editablePageConfig.editable_page_config_content.image[0].list[0].path:''"></em>
      <ul class="scroll-content" :style="{top}" >
        <li v-for="index in editablePageConfig.editable_page_config_content.text[1].count" :key="index" @click="goAdUrl(editablePageConfig.editable_page_config_content.link[1].list[index-1]?editablePageConfig.editable_page_config_content.link[1].list[index-1].content:'')">
          {{editablePageConfig.editable_page_config_content.text[1].list[index-1]?editablePageConfig.editable_page_config_content.text[1].list[index-1].content:''}}
        </li>
      </ul>
    </div>
  </div>
</template>

<script>
export default {
  name: 'Model13',
  data () {
    return {
      activeIndex: 0
    }
  },
  props: ['editablePageConfig'],
  computed: {
    top () {
      return -this.activeIndex * 2 + 'rem'
    }
  },
  beforeDestroy: function () {
    clearInterval(this.time_interval)
  },
  mounted () {
    let _this = this
    this.time_interval = setInterval(function () {
      if (_this.activeIndex < _this.editablePageConfig.editable_page_config_content.text[1].count-1) {
        _this.activeIndex += 1
      } else {
        _this.activeIndex = 0
      }
    }, 2000)
  },
  methods: {
    goAdUrl (url) {
      window.location.href = url
    }
  }
}
</script>

<style lang="scss" scoped>
  .index-article{
    height: 2rem;
    line-height:2rem;
    overflow: hidden;
    display:flex;
    background:#fff;
  }
  .title{width:25%;text-align:center;line-height: 2rem;font-size: 0}
  .title img{max-width:90%;max-height: 90%}
  .more{width:15%;font-size:0.6rem;text-align:center;}
  .scroll-content{
    width:60%;
    position: relative;
    transition: top 0.5s;
    li{
      line-height: 2rem;
      text-align: left;
      font-size:0.6rem;
    }
  }
</style>
