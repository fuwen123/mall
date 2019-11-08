<template>
<div class="container">
    <div class="common-header-wrap">
        <mt-header title="评价管理" class="common-header">
            <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
    </div>

    <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
        <div  v-if="evaluate_list.length > 0">
        <div class="mb-10 evaluate-item"  v-for="(item, index) in evaluate_list">
            <div class="evaluate-info">
                <div class="p-img">
                    <img :src="item.geval_goodsimage_url"/>
                    <span class="common-score-wrapper"><i class="iconfont front" :style="{width:item.geval_scores/5*100+'%'}"></i><i class="iconfont back"></i></span>
                </div>
                <div class="p-info">
                    <div class="name">{{item.geval_content}}</div>
                    <div class="explain">{{item.geval_explain}}</div>
                </div>
            </div>
            <div class="evaluate-btn">
                <div class="buyer-info">{{item.geval_frommembername}} 发表于 {{ $moment.unix(item.geval_addtime).format('YYYY年MM月DD日') }}</div>
                <div class="btn-wrapper"><mt-button size="small" type="primary" class="btn" v-if="!item.geval_explain" @click="goExplain(item.geval_id)">解释</mt-button></div>
            </div>
        </div>
        </div>
        <empty-record v-else></empty-record>
    </div>
</div>
</template>

<script>
import { Indicator, Toast, MessageBox } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getEvaluateList, addExplain } from '../../../api/sellerEvaluate'

export default {
  name: 'Goodsonline',
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      pageTotal: 1, // 总页数
      evaluate_list: [], // 商品列表
      keyword: '',
      goods_type: '',
      search_type: ''

    }
  },
  components: {
    EmptyRecord
  },
  created () {
  },
  methods: {
    setOrderNavActive (index) {
      this.goods_type = index
      this.reload()
    },
    getGoodsList (ispush) {
      Indicator.open()
      let params = this.params
      getEvaluateList(params).then(res => {
        Indicator.close()

        if (ispush) {
          this.evaluate_list = this.evaluate_list.concat(res.result.goodsevallist)
        } else {
          this.evaluate_list = res.result.goodsevallist
        }
        this.pageTotal = res.result.page_total
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
      if (this.isMore && this.params.page <= this.pageTotal) {
        this.loading = false
        this.getGoodsList(true)
      }
    },
    goExplain (geval_id) {
      MessageBox.prompt('请输入解释', '').then(({ value, action }) => {
        addExplain(geval_id, value).then(res => {
          Toast(res.message)
          this.$router.go(0)
        }).catch(function (error) {
          Toast(error.message)
        })
      })
    },
    reload () {
      // 重新加载数据
      this.params.page = 0
      this.isMore = true
      this.evaluate_list = []
      this.loadMore()
    }
  }
}
</script>

<style scoped lang="scss">
    .order-header {
        position: fixed;
        height: 2.2rem;
        width: 100%;
        top: 2.2rem;
        z-index: 100;
        ul {
            list-style: none;
            width: auto;
            display: flex;
            justify-content: space-around;
            align-content: center;
            align-items: center;
            height: 100%;
            background: rgba(255, 255, 255, 1);
            border-bottom: 1px solid #e8eaed;
            li {
                font-size: 0.7rem;
                color: #333;
                height: 100%;
                text-align: center;
                line-height: 2.2rem;
                border-bottom: 0.1rem solid transparent;
                &.active {
                    color: $primaryColor;
                    border-bottom-color: $primaryColor;
                }
            }
        }
    }
    .evaluate-item{background:#fff;}
    .evaluate-info{padding:.5rem;display: flex}
    .evaluate-info .p-img{width:4rem;}
    .evaluate-info .p-img img{width:4rem;height:4rem;}
    .evaluate-info .p-info{flex:1;margin-left:1rem;}
    .evaluate-info .p-info .name{font-size:0.7rem;}
    .evaluate-info .p-info .explain{font-size:0.7rem;color:$primaryColor;margin-top:.5rem}
    .evaluate-btn{padding:.5rem;border-top:1px solid #e4e4e4;display: flex}
    .evaluate-btn .btn-wrapper{width:3rem;}
    .evaluate-btn .btn{float: right}
    .evaluate-btn .buyer-info{flex:1;font-size:.7rem;line-height:1.65rem}
</style>
