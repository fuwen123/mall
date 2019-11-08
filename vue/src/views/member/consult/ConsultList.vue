<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="商品咨询" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>

        <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
            <div  v-if="consult_list && consult_list.length">
                <div class="consult-item"  v-for="(item, index) in consult_list">
                    <div class="consult-info">
                        <div class="p-info">
                            <div class="explain">{{consult_type[item.consulttype_id].consulttype_name}}<span class="right">{{ $moment.unix(item.consult_addtime).format('YYYY年MM月DD日') }}</span></div>
                            <div class="name">咨询内容：{{item.consult_content}}</div>
                            <div class="replay" v-if="item.consult_reply">商家回复：{{item.consult_reply}}</div>
                        </div>
                    </div>
                </div>
            </div>
            <empty-record v-else-if="consult_list && !consult_list.length"></empty-record>
        </div>
    </div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getConsultList } from '../../../api/memberConsult'
export default {
  data () {
    return {
      consult_type: {},
      params: { 'page': 0, 'per_page': 10 },
      consult_list: false,
      loading: false, // 是否加载更多
      isMore: true // 是否有更多
    }
  },
  components: {
    EmptyRecord
  },
  computed: {

  },
  mounted () {
  },
  created () {

  },
  watch: {

  },
  methods: {

    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getConsultList(true)
      }
    },
    getConsultList () {
      Indicator.open()

      getConsultList(this.params).then(res => {
        Indicator.close()
        if (res.result.hasmore) {
          this.isMore = true
        } else {
          this.isMore = false
        }

        let temp = res.result.consult_list
        if (temp) {
          if (!this.consult_list) {
            this.consult_list = temp
            this.consult_type = res.result.consult_type
          } else {
            this.consult_list = this.consult_list.concat(temp)
          }
        }
      }).catch(function (error) {
        Indicator.close()
        Toast(error.message)
      })
    }
  }

}
</script>

<style scoped lang="scss">
    .consult-item{background:#fff;border-bottom:1px solid #e1e1e1}
    .consult-info{padding:.5rem;display: flex}
    .consult-info .p-info{flex:1;margin-left:1rem;}
    .consult-info .p-info .name{font-size:0.6rem;}
    .consult-info .p-info .explain{font-size:0.7rem;color:$primaryColor;margin-bottom:.5rem}
    .consult-info .p-info .explain .right{float:right;color:#666;font-size:0.6rem;}
    .consult-info .p-info .replay{border-top:1px dashed #e1e1e1;font-size:.6rem;margin-top:.5rem;padding-top:.5rem;color:green}
</style>
