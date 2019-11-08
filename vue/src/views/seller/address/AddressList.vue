<template>
	<div class="distributor-address-list">
		<div class="common-header-wrap">
			<mt-header title="发货地址" class="common-header">
				<mt-button slot="left" icon="back" @click="goBack"></mt-button>
				<mt-button slot="right"  @click="goAdd">新增</mt-button>
			</mt-header>
		</div>
		<div v-if="address_list.length>0">
			<div v-for="item in address_list" :key="item.daddress_id">
				<div class="container">
					<div class="top-wrapper">
						<div class="title-wrapper">
							<label class="title">{{ item.seller_name }}</label>
							<label class="title">{{ item.daddress_telphone }}</label>
						</div>
						<label class="desc address-text" style="-webkit-box-orient:vertical">{{item.area_info}}&nbsp;{{item.daddress_detail}}</label>
						<div class="bottom-line"></div>
					</div>
					<div class="bottom-wrapper">
						<div class="bottom-left-wrapper" @click="onDefault(item)">
							<label class="subtitle">
								<i v-if="item.daddress_isdefault == 1" class="iconfont">&#xe69d;</i><i v-else class="iconfont">&#xe69e;</i>默认地址
							</label>
						</div>
						<div class="bottom-right-wrapper">
							<div class="edit-wrapper" @click="onEdit(item.daddress_id)">
								<label class="subtitle">编辑</label>
							</div>
							<div class="edit-wrapper delete-wrapper" @click="onDelete(item.daddress_id)">
								<label class="subtitle">删除</label>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<div  v-else>
			<empty-record></empty-record>
		</div>

	</div>
</template>

<script>

import { Toast, Indicator } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { getAddressList, delAddress, editAddress } from '../../../api/sellerAddress'
export default {
  components: {
    EmptyRecord
  },
  name: 'SellerAddressList',
  data () {
    return {
      address_list: []
    }
  },

  created: function () {
    this.getAddressList()
  },
  methods: {
    goBack () {
      this.$router.go(-1)
    },
    goAdd () {
      this.$router.push({ name: 'SellerAddressForm', query: { action: 'add' } })
    },
    onDefault (address_info) {
      address_info.daddress_isdefault = 1
      editAddress(address_info, address_info.daddress_id).then(
        (response) => {
          Indicator.close()
          this.getAddressList()
        }, (error) => {
          Indicator.close()
          Toast(error.message)
        })
    },
    onEdit (addressId) {
      this.$router.push({ name: 'SellerAddressForm', query: { address_id: addressId } })
    },
    onDelete (addressId) {
      Indicator.open()
      delAddress(addressId).then(
        (response) => {
          this.getAddressList()
          Indicator.close()
        }, (error) => {
          Indicator.close()
          Toast(error.message)
        })
    },
    getAddressList () {
      getAddressList().then(res => {
        this.address_list = res.result.address_list
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  }
}
</script>
<style  lang="scss" scoped>
	.distributor-address-list {
		.container {
			display: flex;
			flex-direction: column;
			justify-content: flex-start;
			align-items: stretch;
			background-color: #fff;
			border-bottom: 1px solid #e8eaed;
		}
		.top-wrapper {
			position: relative;
			flex: 1;
			display: flex;
			flex-direction: column;
			justify-content: flex-start;
			align-items: stretch;
		}
		.title-wrapper {
			height: 1rem;
			display: flex;
			flex-direction: row;
			justify-content: flex-start;
			align-items: center;
			margin-top: 0.5rem;
			margin-left: 0.5rem;
		}
		.title {
			font-size: 0.8rem;
			color: #333;
			margin-left: 0.5rem;
		}
		.default {
			width: 1.4rem;
			margin-left: 0.5rem;
			margin-right: 0.5rem;
			border: 1px solid #e93b3d;
			color: $primaryColor;
			font-size: 0.5rem;
			text-align: center;
			border-radius: 0.1rem;
		}
		.desc {
			color: #7c7f88;
			font-size: 0.7rem;
		}
		.address-text {
			margin-top: 0.5rem;
			margin-bottom: 0.5rem;
			margin-left: 1rem;
			margin-right: 0.5rem;
		}
		.bottom-line {
			position: absolute;
			height: 1px;
			left: 0.5rem;
			bottom:0;
			right: 0.5rem;
			background-color: #e8eaed;
		}
		.bottom-wrapper {
			height: 2.5rem;
			display: flex;
			flex-direction: row;
			justify-content: space-around;
			align-items: stretch;
		}
		.bottom-left-wrapper {
			display: flex;
			flex-direction: row;
			justify-content: flex-start;
			align-items: center;
		}
		.bottom-right-wrapper {
			flex: 1;
			display: flex;
			flex-direction: row;
			justify-content: flex-end;
			align-items: stretch;
		}
		.edit-wrapper {
			display: flex;
			flex-direction: row;
			justify-content: flex-start;
			align-items: center;
		}
		.delete-wrapper {
			margin-right: 0.5rem;
		}
		.indicator {
			width: 0.95rem;
			height: 0.95rem;
			margin-left: 0.75rem;
			margin-right: 0.5rem;
		}
		.icon {
			width: 0.9rem;
			height: 0.9rem;
			margin-left: 0.5rem;
		}
		.subtitle {
			font-size: 0.7rem;
			color: #7c7f88;
			margin-top: 0.5rem;
			margin-bottom: 0.5rem;
			margin-left: 1rem;
			margin-right: 0.5rem;
		}
		.subtitle i{font-size:0.8rem;margin-right:.5rem;}
	}

</style>
