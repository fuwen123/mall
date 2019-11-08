<template>
	<div class="container">
		<div class="common-header-wrap">
			<mt-header :title="getTitle" class="common-header">
				<mt-button slot="left" icon="back" @click="goBack"></mt-button>
			</mt-header>
		</div>

		<mt-field label="姓名" v-model="address_info.address_realname"></mt-field>
		<mt-field label="手机" v-model="address_info.address_mob_phone"></mt-field>
		<mt-cell title="地区">
			<div @click="onRegion">
				<span>{{address_info.area_info}}</span>
				<span class="iconfont right-arrow">&#xe600;</span>
			</div>
		</mt-cell>
		<mt-field label="地址" v-model="address_info.address_detail"></mt-field>
		<!--<mt-cell class="input-wrap" title="地址"><input type="text" v-model="address_info.address_detail"><i class="iconfont" @click="popMap=true;getPosition()">&#xe6d3;</i></mt-cell>-->
		<mt-cell title="默认地址"><mt-switch v-model="address_info.address_is_default"></mt-switch></mt-cell>
		<mt-button class="ds-button-large mt-10" type="primary" v-on:click="submit">{{getSumitTitle}}</mt-button>
		<region-picker ref="picker" v-on:onConfirm="onPickerConfirm"></region-picker>

		<!--&lt;!&ndash;地图选点&ndash;&gt;-->
		<!--<mt-popup v-model="popMap" position="right" class="common-popup-wrapper">-->
			<!--<div class="common-header-wrap">-->
				<!--<mt-header title="定位" class="common-header">-->
					<!--<mt-button slot="left" icon="back" @click="popMap=false"></mt-button>-->
				<!--</mt-header>-->
			<!--</div>-->
			<!--<div class="common-popup-content">-->
				<!--<map-select ref="map_select" :longitude="address_info.address_longitude" :latitude="address_info.address_latitude" @setPosition="setPosition"></map-select>-->
			<!--</div>-->
		<!--</mt-popup>-->
	</div>
</template>

<script>
import { getPointByAddress } from '../../../util/bmap'
import { Toast, Indicator } from 'mint-ui'
import RegionPicker from '../../../components/RegionPicker'
import MapSelect from '../../../components/MapSelect'
import { getAddressInfo, addAddress, editAddress } from '../../../api/memberAddress'
export default {
  components: {
    RegionPicker,
    // MapSelect
  },
  computed: {
    isAddMode () {
      let mode = this.$route.query.action
      // add: 添加地址，edit: 编辑地址
      if (mode === 'add') {
        return true
      } else {
        return false
      }
    },
    getTitle () {
      if (this.isAddMode) {
        return '新增地址'
      } else {
        return '修改收货地址'
      }
    },
    getSumitTitle () {
      let isFromCheckout = this.$route.query.isFromCheckout
      if (isFromCheckout) {
        return '保存并使用'
      } else {
        return '保存'
      }
    }
  },
  data () {
    return {
      popMap: false,
      address_id: 0,
      address_info: {
        address_realname: '',
        address_mob_phone: '',
        address_detail: '',
        address_is_default: true,
        area_info: '请选择地区',
        city_id: 0,
        area_id: 0,
        address_longitude: 0,
        address_latitude: 0
      }
    }
  },
  created: function () {
    if (!this.isAddMode) {
      this.address_id = this.$route.query.address_id
      getAddressInfo(this.address_id).then(res => {
        this.address_info.address_realname = res.result.address_info.address_realname
        this.address_info.address_mob_phone = res.result.address_info.address_mob_phone
        this.address_info.address_detail = res.result.address_info.address_detail
        this.address_info.area_info = res.result.address_info.area_info
        this.address_info.city_id = res.result.address_info.city_id
        this.address_info.area_id = res.result.address_info.area_id
        this.address_info.address_longitude = res.result.address_info.address_longitude
        this.address_info.address_latitude = res.result.address_info.address_latitude
        if (res.result.address_info.address_is_default === '1') {
          this.address_info.address_is_default = true
        } else {
          this.address_info.address_is_default = false
        }
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  },
  methods: {
    goBack () {
      this.$router.go(-1)
    },
    getPosition () {
      getPointByAddress(this.address_info.area_info + this.address_info.address_detail).then(res => {
        if (res.result.location) {
          this.address_info.address_longitude = res.result.location.lng
          this.address_info.address_latitude = res.result.location.lat
        }
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    setPosition (lat, lng, name) {
      this.popMap = false
      this.address_info.address_detail = name
      this.address_info.address_longitude = lng
      this.address_info.address_latitude = lat
    },
    submit () {
      if (this.address_info.address_realname === '') {
        Toast('请填写收件人姓名')
        return
      }
      if (this.address_info.address_realname.length === 0) {
        Toast('请填写收件人姓名')
        return
      }
      if (this.address_info.address_realname.length < 2 || this.address_info.address_realname.length > 15) {
        Toast('2-15个字符限制')
        return
      }
      if (this.address_info.address_mob_phone === '') {
        Toast('请填写手机号码')
        return
      }
      if (this.address_info.address_mob_phone.length === 0) {
        Toast('请填写手机号码')
        return
      }
      if (this.address_info.area_id === 0 || this.address_info.area_id === undefined) {
        Toast('请选择所在地区')
        return
      }
      if (this.address_info.address_detail === '') {
        Toast('请填写详细地址')
        return
      }
      if (this.address_info.address_detail.length === 0) {
        Toast('请填写详细地址')
      }
      if (this.address_info.address_longitude === 0 || this.address_info.address_latitude === 0) {
        // Toast('请在地图选址')
      }

      if (this.isAddMode) {
        Indicator.open()
        addAddress(this.address_info).then(
          (response) => {
            Indicator.close()
            this.updateSelectedAddress()
          }, (error) => {
            Indicator.close()
            Toast(error.message)
          })
      } else {
        Indicator.open()
        editAddress(this.address_info, this.address_id).then(
          (response) => {
            Indicator.close()
            this.updateSelectedAddress()
          }, (error) => {
            Indicator.close()
            Toast(error.message)
          })
      }
    },
    onRegion (picker, values) {
      this.$refs.picker.currentValue = true
    },
    onPickerConfirm (values) {
      this.address_info.area_info = this.getRegionStr(values)

      this.address_info.area_id = values[2].area_id
      this.address_info.city_id = values[1].area_id
    },
    getRegionStr (values) {
      let title = ''
      for (let i = 0; i < values.length; i++) {
        const element = values[i]
        if (i !== 0) {
          title = title + ' ' + element.area_name
        } else {
          title = title + element.area_name
        }
      }
      return title
    },
    updateSelectedAddress () {
      let isFromCheckout = this.$route.query.isFromCheckout
      let goBackLevel = this.$route.query.goBackLevel ? this.$route.query.goBackLevel : -1
      if (isFromCheckout) {
        this.$router.go(goBackLevel)
      } else {
        this.goBack()
      }
    }
  }
}
</script>

<style lang="scss" scoped>
	.right-arrow{transform: rotate(-90deg);color:#ddd;font-size: .6rem;display: inline-block;}
	.input-wrap{position: relative;
		i{position: absolute;right:0;top:0;line-height: 2.4rem;display: block;width:2rem;text-align: center;font-size: 1rem}
	}
</style>
