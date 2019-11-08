<template>
<div class="container">
    <div class="common-header-wrap">
        <mt-header title="商家投诉" class="common-header">
            <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
    </div>

    <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
        <div  v-if="complaintList && complaintList.length">
            <div class="mb-10 goods-item"  v-for="(item, index) in complaintList">
                <div class="goods-info">
                    <div class="p-img">
                        <img :src="goodsList[item.order_goods_id].goods_image_url"/>
                    </div>
                    <div class="p-info">
                        <div class="explain">{{item.complain_subject_content}}</div>
                        <div class="name">{{item.complain_content}}</div>

                    </div>
                </div>
                <div class="goods-btn">
                    <div class="buyer-info">投诉于 {{ $moment.unix(item.complain_datetime).format('YYYY年MM月DD日') }}</div>
                    <div class="btn-wrapper"><mt-button size="small" type="primary" class="btn" v-if="item.complain_state=='10'" @click="cancelComplaint(item.complain_id)">取消</mt-button></div>
                    <div class="btn-wrapper" v-if="item.complain_state==30">
                        <mt-button size="small" type="primary" class="btn" @click="handleComplain(item.complain_id)">提交仲裁</mt-button>
                    </div>
                    <div class="btn-wrapper"><mt-button size="small" type="default" class="btn" @click="$router.push({name:'MemberComplaintForm',query:{complain_id:item.complain_id}})">查看</mt-button></div>
                </div>
            </div>
        </div>
        <empty-record v-else-if="complaintList && !complaintList.length"></empty-record>
    </div>
</div>
</template>

<script>
import { Indicator, MessageBox, Toast } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getComplaintList, cancelComplaint, handleComplain } from '../../../api/memberCompliant'
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
      complaintList: false, // 商品列表
      goodsList: false
    }
  },
  methods: {
    handleComplain (complaint_id) {
      handleComplain(complaint_id).then(res => {
        Toast(res.message)
        this.$router.go(0)
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    cancelComplaint (order_id) {
      MessageBox.confirm('你确定要取消吗？').then(action => {
        cancelComplaint(order_id).then(res => {
          Toast(res.message)
          this.$router.go(0)
        }).catch(function (error) {
          Toast(error.message)
        })
      })
    },
    getComplaintList (ispush) {
      Indicator.open()
      let params = this.params
      getComplaintList(params).then(res => {
        Indicator.close()
        if (res) {
          if (ispush && this.complaintList) {
            this.complaintList = this.complaintList.concat(res.result.complaint_list)
            this.goodsList = Object.assign(this.goodsList, res.result.goods_list)
          } else {
            this.complaintList = res.result.complaint_list
            this.goodsList = res.result.goods_list
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
        this.getComplaintList(true)
      }
    }

  }
}
</script>

<style scoped lang="scss">
    .goods-item{background:#fff;}
    .goods-info{padding:.5rem;display: flex}
    .goods-info .p-img{width:4rem;}
    .goods-info .p-img img{width:4rem;height:4rem;}
    .goods-info .p-info{flex:1;margin-left:1rem;}
    .goods-info .p-info .name{font-size:0.8rem;}
    .goods-info .p-info .explain{font-size:0.8rem;color:$primaryColor;margin-bottom:.5rem}
    .goods-btn{padding:.5rem;border-top:1px solid #e4e4e4;display: flex}
    .goods-btn .btn-wrapper{margin-left:.5rem}
    .goods-btn .btn{float: right}
    .goods-btn .buyer-info{flex:1;font-size:.7rem;line-height:1.4rem}
</style>
