<template>
    <div class="container">
        <home-common-search :value="params.keyword"></home-common-search>
        <goodslist-body></goodslist-body>
    </div>
</template>

<script>
import HomeCommonSearch from '../common/HomeCommonSearch'
import GoodslistBody from './GoodslistBody'
export default {
  name: 'HomeGoodslist',
  components: {
    HomeCommonSearch,
    GoodslistBody
  },
  data () {
    return {
      params: {
        keyword: this.$route.query.keywords ? this.$route.query.keywords : ''
      }

    }
  },
  created () {

  }
}
</script>

<style scoped>

</style>
