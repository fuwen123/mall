<template>
  <div class="container">
    <div class="common-header-wrap">
      <mt-header title="充值卡明细" class="common-header">
        <mt-button slot="left" icon="back" @click="goBack"></mt-button>
        <mt-button slot="right" @click="addRecharge">充值</mt-button>
      </mt-header>
    </div>
    <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
    <div
      class="list-wrapper"
      v-if="predeposit_list && predeposit_list.length"

    >
      <div
        class="container"
        v-for="(item, index) in predeposit_list"
        :key="item.rcblog_id"
      >
        <label
          class="title"
          v-bind:class="item.available_amount > 0 ? 'income' : 'expend'"
          >{{ item.available_amount }}</label
        >
        <label class="subtitle">{{ item.rcblog_description }}</label>
        <label class="date">{{ item.add_time_text }}</label>
      </div>
    </div>
    <empty-record v-else-if="predeposit_list && !predeposit_list.length"></empty-record>
    </div>

  </div>
</template>

<script>
import { getRechargeCardList,addRechargeCard } from '../../../api/memberPredeposit'
import { Toast, Indicator,MessageBox } from 'mint-ui'
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
      predeposit_list: false,
      rc_sn:'',
    }
  },
  created () {

  },

  mounted () {
    this.wrapperHeight = document.documentElement.clientHeight - 90
  },
  methods: {
    addRecharge(){
      MessageBox.prompt('充值卡号', '').then(({ value, action }) => {
        if (value) {
          addRechargeCard(value).then(res => {
            Toast(res.message)
            this.params={ 'page': 0, 'per_page': 10 }
            this.loading=false
            this.isMore= true
            this.predeposit_list=false
            this.loadMore()
          }).catch(function (error) {
            Toast(error.message)
          })
        }
      })
    },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getRechargeCardList(true)
      }
    },
    goBack () {
      this.$router.go(-1)
    },
    getRechargeCardList () {
      Indicator.open()

          getRechargeCardList(this.params).then(res => {
            Indicator.close()
            if (res.result.hasmore) {
              this.isMore = true
            } else {
              this.isMore = false
            }

            let temp = res.result.log_list
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
  margin-top: 2.2rem;
  height: 2rem;
  z-index: 100;
}
.list-wrapper {
	.container {
	display: flex;
	position: relative;
	flex-direction: column;
	justify-content: flex-start;
	align-items: stretch;
	background-color: #fff;
	border-bottom: 1px solid #e8eaed;
	}
	.title {
	font-size: 0.9rem;
	margin-left: 0.7rem;
	margin-top: 0.7rem;
	}
	.income {
	color: $primaryColor;
	}
	.expend {
	color: #666666;
	}
	.subtitle {
	font-size: 0.6rem;
	color: #999999;
	margin-left: 0.7rem;
	margin-top: 0.4rem;
	margin-bottom: 0.5rem;
	}
	.date {
	color: #999999;
	font-size: 0.7rem;
	position: absolute;
	top: 0.9rem;
	right: 0.7rem;
	}
}
</style>
