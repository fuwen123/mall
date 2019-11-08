<template>
    <div class="distributor-article-list">
      <div class="common-header-wrap">
        <mt-header :title="article_title" class="common-header">
          <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
      </div>
      <div class="content" v-html='article_content'></div>
    </div>
</template>
<script>
import { getArticleDetail } from '../../../api/homeArticle'
import { Toast } from 'mint-ui'
export default {

  name: 'HomeDocument',
  data () {
    return {
      article_title: '',
      article_content: ''
    }
  },

  created: function () {
    let article_id = this.$route.query.article_id
    getArticleDetail(article_id).then(res => {
      this.article_title = res.result.article_title
      this.article_content = res.result.article_content
    }).catch(function (error) {
      Toast(error.message)
    })
  }

}
</script>
<style  lang="scss" scoped>
.content{padding:.5rem;font-size:.7rem;line-height:1rem;}
</style>
