<template>
    <div>
      <home-common-search :value="params.keyword"></home-common-search>
      <common-store-list />
    </div>
</template>

<script>
import { mapState, mapMutations } from 'vuex'
import HomeCommonSearch from '../common/HomeCommonSearch'
import CommonStoreList from '../common/CommonStoreList'
export default {
  name: 'HomeStorelist',
  components: {
    HomeCommonSearch,
    CommonStoreList
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
