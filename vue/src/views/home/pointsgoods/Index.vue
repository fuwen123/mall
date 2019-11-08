<template>
<div>
    <div class="common-header-wrap">
        <mt-header class="common-header" title="积分礼品">
            <mt-button icon="back" slot="left" @click="$router.go(-1)"></mt-button>
        </mt-header>
    </div>
<ul class="goods-list" v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
<li v-for="item in goodsList" :key="item.pgoods_id" @click="goPointsgoodsDetail(item.pgoods_id)">
<div class="product-div">
<div class="p-img">
    <img :src="item.pgoods_image">
</div>
    <div class="p-info">
        <h5>{{item.pgoods_name}}</h5>
        <p><span class="strong">{{item.pgoods_points}}</span>积分</p>
        <p class="mb-5">市场价： ¥{{item.pgoods_price}}</p>
        <span class="btn">立即兑换</span>
    </div>
</div>
</li>
</ul>
    <empty-record  v-if="goodsList.length === 0"></empty-record>
</div>
</template>

<script>
import { Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getPointsgoodsList } from '../../../api/homePointsgoods'
export default {
  name: 'index',
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      pageTotal: 1, // 总页数
      goodsList: [] // 商品列表
    }
  },
  components: {
    EmptyRecord
  },
  methods: {
    getPointsgoodsList (ispush) {
      Indicator.open()
      let params = this.params
      getPointsgoodsList(params).then(res => {
        Indicator.close()

            if (ispush) {
              this.goodsList = this.goodsList.concat(res.result.goods_list)
            } else {
              this.goodsList = res.result.goods_list
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
        this.getPointsgoodsList(true)
      }
    },
    // 积分兑换详情页
    goPointsgoodsDetail (pgoods_id) {
      this.$router.push({ name: 'HomePointsgoodsDetail', query: { pgoods_id: pgoods_id } })
    }
  }
}
</script>

<style scoped lang="scss">
.goods-list{}
.goods-list li{width: 100%;border-bottom: 1px solid #f6f6f9;}
.product-div{padding: .8rem;background: #fff;position: relative;overflow: hidden;}
.product-div .p-img{width:5.2rem;height: 5.2rem;line-height: 5.2rem;text-align: center;border:1px solid #e1e1e1;float: left;}
.product-div .p-img img{width: 5.2rem;max-height: 5.2rem;}
.product-div .p-info{margin-left:6rem;height:5.2rem;padding: 0;}
.product-div .p-info h5{font-size:.8rem;line-height:1rem;height:1rem;overflow:hidden;text-overflow:ellipsis;margin-bottom: .2rem;white-space: nowrap;}
.product-div .p-info p{ color: #777;font-size:.6rem;line-height:1rem;}
.product-div .p-info p .strong{color:$primaryColor;font-size:.8rem;margin-right:.2rem}
.product-div .p-info .btn{border-radius: .2rem;color: #fff;font-size:.7rem;padding:.2rem .5rem;line-heigh:1rem;background: #e93b3d;border: 1px solid #e93b3d;}
</style>
