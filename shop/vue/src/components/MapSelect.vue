<template>
  <div class="common-map-select">
    <div class="search-wrap"><i class="iconfont" @click="searchAddress">&#xe6cb;</i><input type="text" v-model="keyword"><mt-button @click="searchAddress" size="small" plain class="btn">搜索</mt-button></div>
    <!--<div id="common_map_select" :style="{height:.45*wrapperHeight+'px'}"></div>-->
    <div class="result-list" >
      <div :class="{'current':style}" v-if="member_point && style">
        <span class="notice">当前地址<span class="reset"><i class="iconfont" @click="getPosition">&#xe6d3;</i>重新定位</span></span>
        <div>
          <div class="result-item" @click="setPosition(member_point.lat,member_point.lng,member_point.address)">{{member_point.address}}</div>
        </div>
      </div>
      <div :class="{'nearby':style}">
        <span class="notice">附近地址</span>
        <div v-for="(item,index) in address_list" :key="index">
          <div class="result-item" v-if="item.location" @click="setPosition(item.location.lat,item.location.lng,item.name)">{{item.name}}</div>
        </div>
      </div>

    </div>
  </div>
</template>

<script>
import { getAddressByKeyword, getAddressByPoint, getPointNearby, getPosition } from '../util/bmap'
import { mapState, mapMutations } from 'vuex'
import { Toast, Indicator } from 'mint-ui'
export default {
  name: 'MapSelect',
  data () {
    return {
      keyword: '',
      address_list: [],
      // wrapperHeight: 0,
      bmap: false,
      myMarker: false,
      cityCode: 0,
      style: false
    }
  },
  props: {
    longitude: {},
    latitude: {}
  },
  watch: {
    longitude: function (newLongitude, oldLongitude) {
      if (newLongitude) {
        this.creatMap()
      }
    }
  },
  computed: {
    ...mapState({
      member_point: state => state.member.point
    })
  },
  mounted () {
    // this.wrapperHeight = document.documentElement.clientHeight - 80
    // let _this = this
    // loadScript(function () {
    //   _this.wrapperHeight = document.documentElement.clientHeight - 80
    //   _this.bmap = new BMap.Map('common_map_select')
    //
    //   var zoomCtrl = new BMap.ZoomControl({ anchor: BMAP_ANCHOR_BOTTOM_RIGHT, offset: new BMap.Size(20, 20) })
    //   _this.bmap.addControl(zoomCtrl)
    //   _this.creatMap()
    // })
  },
  created () {
    if (this.longitude) {
      this.creatMap()
    }
  },
  methods: {
    ...mapMutations({
      memberPoint: 'memberPoint'
    }),
    getPosition () {
      let _this = this
      Indicator.open()
      getPosition(function (res) {
        Indicator.close()
        if (res.code === 10000) {
          _this.getAddressByPoint(res.result.lat, res.result.lng)
        } else {
          Toast(res.message)
        }
      }, true)
    },
    getAddressByPoint (lat, lng) {
      getAddressByPoint(lat + ',' + lng).then(res => {
        this.cityCode = res.result.cityCode
        this.memberPoint({ point: { lng: lng, lat: lat, address: res.result.sematic_description, cityCode: res.result.cityCode } })
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    creatMap () {
      this.getAddressByPoint(this.latitude, this.longitude)
      // 附近地址
      getPointNearby(this.latitude + ',' + this.longitude).then(res => {
        this.address_list = res.results
        this.style = true
      }).catch(function (error) {
        Toast(error.message)
      })
      // let point = new BMap.Point(this.longitude, this.latitude)
      // 创建点坐标
      // this.bmap.centerAndZoom(point, 16)
      // this.myMarker = new BMap.Marker(point)
      // this.bmap.addOverlay(this.myMarker)
    },
    searchAddress () {
      getAddressByKeyword(this.keyword, this.cityCode).then(res => {
        this.address_list = res.result
        this.style = false
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    setPosition (lat, lng, name) {
      this.$emit('setPosition', lat, lng, name, this.cityCode)
    }
  }
}
</script>
<style  lang="scss" scoped>
  .common-map-select{background: #fff;position: absolute;width:100%;height:100%;
    .search-wrap{height: 2rem;line-height: 2rem;position: relative;position: absolute;left:.5rem;top:.5rem;right:3.5rem;
      input{width: 100%;line-height: 2rem;text-indent: 2rem;font-size: .8rem;display: block;background: #ebebeb;}
      i{position: absolute;left: 0;font-size:1rem;line-height: 2rem;display: block;width:2rem;text-align: center}
      .btn{position: absolute;right:-3rem;top:50%;margin-top:-16.5px}
    }
    .result-list{overflow-y:auto;position: absolute;top:3rem;bottom:0rem;width:100%;
      .notice{display: none;padding:1rem .5rem 0 .5rem;font-size:.7rem;color:#aeaeae;line-height: 1.5}
      .reset{float: right;color:orange}
      .current .notice,.nearby .notice{display: block}
      .result-item{padding:.7rem 0;font-size: .8rem;color:#333;border-bottom: 1px solid #eee;margin:0 0.5rem;}
    }
  }
</style>
