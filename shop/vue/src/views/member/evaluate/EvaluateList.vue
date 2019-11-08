<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header class="common-header" title="我的评价">
                <mt-button icon="back" slot="left" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div class="" v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
            <div  v-if="evaluateList && evaluateList.length">
                <div class="item mt-5"  v-for="evaluate in evaluateList" :key="evaluate.ob_no">
                    <div class="p-img">
                        <img :src="evaluate.geval_goodsimage"/>
                    </div>
                    <div class="p-info">
                        <div class="p-name">{{evaluate.geval_goodsname}}</div>
                        <div class="p-score"><span class="common-score-wrapper"><i class="iconfont front" :style="{width:evaluate.geval_scores/5*100+'%'}"></i><i class="iconfont back"></i></span></div>
                        <div class="p-content">{{evaluate.geval_content}}</div>
                    </div>
                </div>
            </div>
            <empty-record v-if="evaluateList && !evaluateList.length"></empty-record>
        </div>
    </div>
</template>

<script>
import { Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getMemberevaluateList } from '../../../api/memberEvaluate'
export default {
  name: 'EvaluateList',
  components: {
    EmptyRecord
  },
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      evaluateList: false // 商品列表
    }
  },
  methods: {
    getEvaluateList (ispush) {
      Indicator.open()
      let params = this.params
      getMemberevaluateList(params).then(res => {
        Indicator.close()

        if (ispush && this.evaluateList) {
          this.evaluateList = this.evaluateList.concat(res.result.goodsevallist)
        } else {
          this.evaluateList = res.result.goodsevallist
        }
        if (res.result.hasmore) {
          this.isMore = true
        } else {
          this.isMore = false
        }
      })
    },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getEvaluateList(true)
      }
    }
  }
}
</script>

<style scoped>
.item{display: flex;display:-webkit-flex;background:#fff;padding:.5rem;}
.item .p-img{}
.item .p-img img{width:3rem;height:3rem;}
.item .p-info{margin-left:.2rem;}
.item .p-info .p-name{line-height:1rem;font-size:.6rem;}
.item .p-info .p-score{}
.item .p-info .p-content{font-size:.5rem;line-height:.8rem;}
</style>
