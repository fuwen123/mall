<template>
  <div class="distributor-article-list">
    <div class="common-header-wrap">
      <mt-header :title="title"
                 class="common-header">
        <mt-button slot="left"
                   icon="back"
                   @click="$router.go(-1)"></mt-button>
      </mt-header>
    </div>
    <div class="content"
         v-html='content'></div>
  </div>
</template>
<script>
import { getDocumentInfo } from '../../../api/homeArticle'
import { Toast } from 'mint-ui'
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
      this.title = res.result.document_title
      this.content = res.result.document_content
    }).catch(function (error) {
      Toast(error.message)
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
