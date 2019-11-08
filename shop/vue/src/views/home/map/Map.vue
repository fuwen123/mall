<template>
  <div>
    <div class="common-header-wrap">
      <mt-header title="定位" class="common-header">
        <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
      </mt-header>
      <div class="map-wrapper">
        <map-select ref="map_select" :longitude="lng" :latitude="lat" @setPosition="setPosition"></map-select>
      </div>
    </div>
  </div>
</template>

<script>
import { Toast } from 'mint-ui'
import MapSelect from '../../../components/MapSelect'
import { mapState, mapMutations } from 'vuex'
import { getAddressByPoint, getPosition, getPointByIp } from '../../../util/bmap'
export default {
  name: 'HomeMap',
  data () {
    return {
      lng: 0,
      lat: 0
    }
  },
  components: {
    MapSelect
  },
  mounted () {
  },
  created: function () {
    if (!this.member_point) {
      let _this = this
      getPosition(function (res) {
        if (res.code === 10000) {
          _this.lat = res.result.lat
          _this.lng = res.result.lng
          _this.getAddressByPoint()
        } else {
          // Toast(res.message)
          // 使用ip定位
          getPointByIp().then(res => {
            if (res.status == 0) {
              _this.lat = res.content.point.y
              _this.lng = res.content.point.x
              _this.getAddressByPoint()
            } else {
              Toast(res.message)
            }
          }).catch(function (error) {
            Toast(error.message)
          })
        }
      }, true)
    } else {
      this.lat = this.member_point.lat
      this.lng = this.member_point.lng
    }
  },
  computed: {
    ...mapState({
      member_point: state => state.member.point
    })
  },
  methods: {
    ...mapMutations({
      memberPoint: 'memberPoint'
    }),
    getAddressByPoint () {
      getAddressByPoint(this.lat + ',' + this.lng).then(res => {
        if (res.status == 0) {
          this.memberPoint({ point: { lng: this.lng, lat: this.lat, address: res.result.sematic_description, cityCode: res.result.cityCode } })
        } else {
          Toast(res.message)
        }
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    setPosition (lat, lng, name, cityCode) {
      this.memberPoint({ point: { lng: lng, lat: lat, address: name, cityCode: cityCode } })
      this.$router.go(-1)
    }
  }
}
</script>

<style scoped lang="scss">
.map-wrapper{position:absolute;top:2rem;bottom:2.5rem;width:100%}
</style>
