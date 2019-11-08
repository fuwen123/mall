<template>
	<div class="container">
		<div class="common-header-wrap">
			<mt-header :title="getTitle" class="common-header">
				<mt-button slot="left" icon="back" @click="goBack"></mt-button>
			</mt-header>
		</div>
		<mt-field label="联系人" v-model="address_info.seller_name"></mt-field>
		<mt-field label="手机" v-model="address_info.daddress_telphone"></mt-field>
		<mt-cell title="地区">
			<div @click="onRegion">
				<span>{{address_info.area_info}}</span>
				<span class="iconfont right-arrow">&#xe600;</span>
			</div>
		</mt-cell>
		<mt-field label="地址" v-model="address_info.daddress_detail"></mt-field>
		<mt-cell title="默认地址"><mt-switch v-model="address_info.daddress_isdefault"></mt-switch></mt-cell>
		<mt-button class="ds-button-large" type="primary" v-on:click="submit">{{getSumitTitle}}</mt-button>
		<region-picker ref="picker" v-on:onConfirm="onPickerConfirm"></region-picker>
	</div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import RegionPicker from '../../../components/RegionPicker'
import { getAddressInfo, editAddress } from '../../../api/sellerAddress'
export default {
  components: {
    RegionPicker
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
        return '修改发货地址'
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
        seller_name: '',
        daddress_telphone: '',
        daddress_detail: '',
        daddress_isdefault: true,
        area_info: '请选择地区',
        city_id: 0,
        area_id: 0
      }
    }
  },
  created: function () {
    if (!this.isAddMode) {
      this.address_id = this.$route.query.address_id
      getAddressInfo(this.address_id).then(res => {
        this.address_info = res.result.address_info
        if (res.result.address_info.daddress_isdefault === '1') {
          this.address_info.daddress_isdefault = true
        } else {
          this.address_info.daddress_isdefault = false
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
    submit () {
      if (this.address_info.seller_name === '') {
        Toast('请填写联系人姓名')
        return
      }
      if (this.address_info.seller_name.length < 2 || this.address_info.seller_name.length > 15) {
        Toast('2-15个字符限制')
        return
      }
      if (this.address_info.daddress_telphone === '') {
        Toast('请填写手机号码')
        return
      }

      if (this.address_info.area_id === 0 || this.address_info.area_id === undefined) {
        Toast('请选择所在地区')
        return
      }
      if (this.address_info.daddress_detail === '') {
        Toast('请填写详细地址')
        return
      }
      Indicator.open()
      editAddress(this.address_info, this.isAddMode ? 0 : this.address_id).then(
        (response) => {
          Indicator.close()
          this.updateSelectedAddress()
        }, (error) => {
          Indicator.close()
          Toast(error.message)
        })
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
