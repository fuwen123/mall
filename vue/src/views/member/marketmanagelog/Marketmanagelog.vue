<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header title="活动记录" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
      </mt-header>
    </div>
    <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
    <div
      class="list-wrapper"
      v-if="log_list && log_list.length"
    >
      <div
        class="point-list"
        v-for="(item, index) in log_list"
        :key="item.marketmanagelog_id"
      >
        <div class="info">
          <div class="title">{{ item.marketmanagelog_remark }}</div>
          <div class="time">{{ $moment.unix(item.marketmanagelog_time).format('YYYY.MM.DD') }}</div>
        </div>
        <div class="right" :class="item.marketmanagelog_win>0?'number':'diture'">{{ item.marketmanagelog_win>0?'已中奖':'未中奖' }}</div>
      </div>
    </div>
    <empty-record v-else-if="log_list && !log_list.length"></empty-record>
  </div>
  </div>
</template>

<script>
import { getMarketmanagelog } from '../../../api/homeMarketmanage'
import { Toast, Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
export default {
  name: 'BalanceHistory',
  components: {
    EmptyRecord
  },
  data () {
    return {
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      wrapperHeight: 0,

      log_list: false
    }
  },
  created () {

  },

  mounted () {
    this.wrapperHeight = document.documentElement.clientHeight - 90
  },
  methods: {
    goBack () {
      this.$router.go(-1)
    },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getMarketmanagelog(true)
      }
    },
    getMarketmanagelog () {
      Indicator.open()

      getMarketmanagelog(this.params).then(res => {
        Indicator.close()
        if (res.result.hasmore) {
          this.isMore = true
        } else {
          this.isMore = false
        }

        let temp = res.result.log_list
        if (temp) {
          if (!this.log_list) {
            this.log_list = temp
          } else {
            this.log_list = this.log_list.concat(temp)
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
.container {
  height: 100%;
  display: flex;
  position: relative;
  flex-direction: column;
  justify-content: flex-start;
  align-items: stretch;

}
.header {
  border-bottom: 1px solid #e8eaed;
}
.topList {
  position: fixed;
  width: 100%;
  margin-top:2rem;
  height:2rem;
  z-index: 100;
}
.list-wrapper {
  flex: 1;
  display: flex;
  flex-direction: column;
  justify-content: flex-start;
  align-items: stretch;
  .point-list {
    background-color: #fff;
    border-bottom: 1px solid #e8eaed;
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding: 0.55rem 0.75rem;
    .right{width:2.5rem;}
    .info {
      flex:1;
      .title {
        font-size:0.7rem;
        color: #333;
      }
      .time {
        font-size: 0.6rem;
        color: #999;
        margin-top:0.25rem;
      }
    }
    .number {
      font-size: .8rem;
      color:$primaryColor;
    }
    .diture {
      font-size: .8rem;
      color: rgba(102, 102, 102, 1);
    }
  }
}
</style>
