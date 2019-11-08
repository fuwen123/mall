<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="举报商品" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>

        <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
            <div  v-if="informList && informList.length">
                <div class="mb-10 inform-item"  v-for="(item, index) in informList">
                    <div class="inform-info">
                        <div class="p-img">
                            <img :src="item.inform_goods_image_url"/>
                        </div>
                        <div class="p-info">
                            <div class="explain">{{item.inform_subject_content}}</div>
                            <div class="name">{{item.inform_content}}</div>
                        </div>
                    </div>
                    <div class="inform-btn">
                        <div class="buyer-info">举报于 {{ $moment.unix(item.inform_datetime).format('YYYY年MM月DD日') }}</div>
                        <div class="btn-wrapper"><mt-button size="small" type="primary" class="btn" v-if="item.inform_state=='1'" @click="cancelInform(item.inform_id)">取消</mt-button></div>
                        <div class="btn-wrapper"><mt-button size="small" type="default" class="btn" @click="$router.push({name:'MemberInformForm',query:{inform_id:item.inform_id}})">查看</mt-button></div>
                    </div>
                </div>
            </div>
            <empty-record v-else-if="informList && !informList.length"></empty-record>
        </div>
    </div>
</template>

<script>
import { Indicator, MessageBox, Toast } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getInformList, cancelInform } from '../../../api/memberInform'
export default {
  name: 'BillList',
  components: {
    EmptyRecord
  },
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      informList: false, // 商品列表
    }
  },
  methods: {
    cancelInform (order_id) {
      MessageBox.confirm('你确定要取消吗？').then(action => {
        cancelInform(order_id).then(res => {
          Toast(res.message)
          this.$router.go(0)
        }).catch(function (error) {
          Toast(error.message)
        })
      })
    },
    getInformList (ispush) {
      Indicator.open()
      let params = this.params
      getInformList(params).then(res => {
        Indicator.close()
        if (res) {
          if (ispush && this.informList) {
            this.informList = this.informList.concat(res.result.inform_list)
          } else {
            this.informList = res.result.inform_list
          }
          if (res.result.hasmore) {
            this.isMore = true
          } else {
            this.isMore = false
          }
        }
      })
    },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getInformList(true)
      }
    }

  }
}
</script>

<style scoped lang="scss">
    .inform-item{background:#fff;}
    .inform-info{padding:.5rem;display: flex}
    .inform-info .p-img{width:4rem;}
    .inform-info .p-img img{width:4rem;height:4rem;}
    .inform-info .p-info{flex:1;margin-left:1rem;}
    .inform-info .p-info .name{font-size:0.8rem;}
    .inform-info .p-info .explain{font-size:0.8rem;color:$primaryColor;margin-bottom:.5rem}
    .inform-btn{padding:.5rem;border-top:1px solid #e4e4e4;display: flex}
    .inform-btn .btn-wrapper{margin-left:.5rem}
    .inform-btn .btn{float: right}
    .inform-btn .buyer-info{flex:1;font-size:.7rem;line-height:1.4rem}
</style>
