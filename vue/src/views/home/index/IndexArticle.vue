<template>
    <div class="index-article">
        <em class="title"><span style="color:#000">最新</span>资讯</em>
        <ul class="scroll-content" :style="{top}" >
            <li v-for="(item, index) in items" :key="index" @click="goArticleDetail(item)">
                {{item.article_title}}
            </li>
        </ul>
        <em class="more" @click="goHomeArticlelist('1')">更多</em>
    </div>
</template>

<script>
export default {
  name: 'IndexArticle',
  data () {
    return {
      activeIndex: 0
    }
  },
  props: ['items'],
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
      if (_this.activeIndex < (_this.items.length-1)) {
        _this.activeIndex += 1
      } else {
        _this.activeIndex = 0
      }
    }, 2000)
  },
  methods: {
    goHomeArticlelist (acId) {
      this.$router.push({ name: 'HomeArticlelist', query: { ac_id: acId } })
    },
    goArticleDetail (item) {
      this.$router.push({ 'name': 'HomeArticledetail', 'query': { 'article_id': item.article_id } })
    }
  }
}
</script>

<style scoped lang="scss">
    .index-article{
        height: 2rem;
        line-height:2rem;
        overflow: hidden;
        display:flex;
        background:#fff;
    }
    .title{width:25%;text-align:center;font-size:0.7rem;font-weight:700;color:$primaryColor}
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
