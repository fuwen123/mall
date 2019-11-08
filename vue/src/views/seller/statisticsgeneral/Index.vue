<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header title="近30天销售走势" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <my-highcharts id="high" class="high" :option="stattoday_json"></my-highcharts>
    </div>
</template>

<script>
import MyHighcharts from './MyHighcharts'
import { getStatisticsGeneral } from '../../../api/sellerStatistics'
export default {
  name: 'StatisticsGeneral',
  components: {
    MyHighcharts
  },
  data () {
    return {
      goodstop30_arr: '',
      stattoday_json: [],
      statnew_arr: '',
      stat_time: ''
    }
  },
  created () {
    this.getStatisticsGeneral()
  },
  computed: {
  },
  mounted () {
  },
  beforeDestroy () {
  },
  methods: {
    // 最近30天销售数据
    getStatisticsGeneral () {
      getStatisticsGeneral().then(res => {
        if (res) {
          this.goodstop30_arr = res.result.goodstop30_arr
          this.stattoday_json = JSON.parse(res.result.stattoday_json)// 字符串转JSON格式
          this.statnew_arr = res.result.statnew_arr
          this.stat_time = res.result.stat_time
        }
      })
    }
  }
}

</script>

<style scoped>

</style>
