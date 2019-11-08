<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header class="common-header" title="文章分类">
                <mt-button icon="back" slot="left" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div class="class-list">
            <ul>
                <li v-for="item in articleclass_list" v-bind:key="item.ac_id" @click="goHomeArticlelist(item.ac_id)">
                    {{item.ac_name}}
                </li>
            </ul>
        </div>
        <empty-record v-if="articleclass_list && !articleclass_list.length"></empty-record>
    </div>
</template>

<script>
import { getArticleclassList } from '../../../api/homeArticle'
import EmptyRecord from '../../../components/EmptyRecord'
import { Toast, Indicator } from 'mint-ui'
export default {
  components: {
    EmptyRecord
  },
  name: 'Articleclass',
  data () {
    return {
      articleclass_list: false
    }
  },
  created: function () {
    this.getArticleclassList()
  },
  methods: {
    getArticleclassList () {
      Indicator.open()
      getArticleclassList().then(res => {
        Indicator.close()
        this.articleclass_list = res.result.article_class
      }).catch(function (error) {
        Indicator.close()
        Toast(error.message)
      })
    },
    goHomeArticlelist (acId) {
      this.$router.push({ name: 'HomeArticlelist', query: { ac_id: acId } })
    }
  }
}
</script>

<style scoped>
    .class-list{}
    .class-list li{box-sizing: border-box;
        text-align: center;
        padding:.4rem;
        width: 50%;
        float: left;
        background:#fff;
        line-height:2rem;
        font-size:0.8rem;
        border-bottom: 1px solid #dedbdb;
        border-right: 1px solid #dedbdb;}
</style>
