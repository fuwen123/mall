<template>
  <div class="category">
    <home-common-search></home-common-search>
    <goodsclass-body></goodsclass-body>
  </div>
</template>

<script>
import HomeCommonSearch from '../common/HomeCommonSearch'
import GoodsclassBody from './GoodsclassBody'
export default {
  name: 'HomeGoodsclass',
  components: {
    HomeCommonSearch,
    GoodsclassBody
  },
  computed: {
    // isShowTabbar() {
    //   let isFromHome = this.$route.params.isFromHome;
    //   if (isFromHome) {
    //     return false;
    //   }
    //   return true;
    // }
  }
}
</script>

<style scoped lang='scss'>

</style>
