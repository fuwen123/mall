<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header title="资金明细" class="common-header">
        <mt-button slot="left" icon="back" @click="goBack"></mt-button>
      </mt-header>
    </div>
    <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
    <div class="list-wrapper" v-if="predeposit_list && predeposit_list.length">
      <div class="item" v-for="(item, index) in predeposit_list" :key="item.lg_id">
        <label class="title" v-bind:class="item.lg_av_amount > 0 ? 'income' : 'expend'">{{ item.lg_av_amount }}</label>
        <label class="subtitle">{{ item.lg_desc }}</label>
        <label class="date">{{ item.lg_addtime_text }}</label>
      </div>
    </div>
    <empty-record v-else-if="predeposit_list && !predeposit_list.length"></empty-record>
    </div>
  </div>
</template>

<script>
import { getPredepositList } from '../../../api/memberPredeposit'
import { Toast, Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
export default {
  name: 'BalanceHistory',
  components: {
    EmptyRecord
  },
  data () {
    return {
      wrapperHeight: 0,
      params: { 'page': 0, 'per_page': 10 },
      loading: false, // 是否加载更多
      isMore: true, // 是否有更多
      predeposit_list: false
    }
  },
  created () {

  },

  mounted () {
    this.wrapperHeight = document.documentElement.clientHeight - 90
  },
  methods: {
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getPredepositList(true)
      }
    },
    goBack () {
      this.$router.go(-1)
    },
    getPredepositList () {
      Indicator.open()
      getPredepositList(this.params).then(res => {
        Indicator.close()
        if (res.result.hasmore) {
          this.isMore = true
        } else {
          this.isMore = false
        }

        let temp = res.result.list
        if (temp) {
          if (!this.predeposit_list) {
            this.predeposit_list = temp
          } else {
            this.predeposit_list = this.predeposit_list.concat(temp)
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
  margin-top:2.2rem;
  height:2rem;
  z-index: 100;
}
.list-wrapper {
	.item {
	display: flex;
	position: relative;
	flex-direction: column;
	justify-content: flex-start;
	align-items: stretch;
	background-color: #fff;
	border-bottom: 1px solid #e8eaed;
	}
	.title {
	font-size:0.9rem;
	margin-left:0.7rem;
	margin-top:0.7rem;
	}
	.income {
	color: $primaryColor;
	}
	.expend {
	color: #333;
	}
	.subtitle {
	font-size:0.6rem;
	color: #666;
	margin-left:0.7rem;
	margin-top:0.4rem;
	margin-bottom:0.5rem;
	}
	.date {
	color: #666;
	font-size:0.7rem;
	position: absolute;
	top:0.9rem;
	right: 0.7rem;
	}
}
</style>
