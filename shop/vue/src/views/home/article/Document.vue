<template>
  <div class="distributor-article-list">
    <div class="content"
         v-html='content'></div>
  </div>
</template>
<script>
import { getDocumentInfo } from '@/api/homeArticle'
export default {
  name: 'HomeDocument',
  data () {
    return {
      title: '',
      content: ''
    }
  },
  created: function () {
    let type = this.$route.query.type
    getDocumentInfo(type).then(res => {
      this.$route.meta.head.title = res.result.document_title
      this.content = res.result.document_content
    }).catch(function (error) {
      this.$tosat(error.message)
    })
  }

}
</script>
<style  lang="scss" scoped>
.content {
  padding: 0.5rem;
  font-size: 0.7rem;
}
</style>
