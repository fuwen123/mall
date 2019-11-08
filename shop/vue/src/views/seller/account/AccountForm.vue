<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header :title="getTitle" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
                <mt-button slot="right"  @click="onDelete" v-if="!isAddMode">删除</mt-button>
            </mt-header>
        </div>
        <div v-if="isAddMode">
            <mt-field label="商城账号" type="text" v-model="seller.member_name"></mt-field>
            <mt-field label="卖家账号" type="text" v-model="seller.seller_name"></mt-field>
            <mt-field label="密码" type="password" v-model="seller.password"></mt-field>
        </div>
        <div v-else>
            <mt-cell title="卖家账号">{{seller.seller_name}}</mt-cell>
        </div>
        <div class="select" @click="popupVisible = true" >
            <mt-cell title="账号组" is-link>
                <span>{{seller.sellergroup_name?seller.sellergroup_name: '请选择'}}</span>
            </mt-cell>
        </div>
        <mt-button class="button mt-10" type="primary" v-on:click="submit">提交</mt-button>
        <mt-popup v-model="popupVisible" position="bottom" class="mint-popup">
            <mt-picker :slots="sellerGroupList" @change="onSellerGroupChange" ref="picker" :visible-item-count="5" value-key="sellergroup_name"></mt-picker>
            <mt-button @click="handleConfirm" type="primary" class="ds-button-large" >确认</mt-button>
        </mt-popup>

    </div>
</template>

<script>
import { Toast, Indicator, MessageBox } from 'mint-ui'
import { getSellerAccountInfo, getSellerAccountGroupList, delSellerAccount, editSellerAccount, addSellerAccount } from '../../../api/sellerAccount'
export default {
  name: 'AccountForm',
  data () {
    return {
      popupVisible: false,
      sellergroup_list: [], // 账号组
      seller: {
        'seller_id': 0,
        'member_name': '', // 商城用户名
        'seller_name': '', // 卖家用户名
        'password': '',
        'sellergroup_id': 0,
        'sellergroup_name': ''
      }
    }
  },
  created: function () {
    // 获取店铺的账户组
    getSellerAccountGroupList().then(res => {
      this.sellergroup_list = res.result.sellergroup_list
    }).catch(function (error) {
      Toast(error.message)
    })

    if (!this.isAddMode) {
      this.seller.seller_id = this.$route.query.seller_id
      getSellerAccountInfo(this.seller.seller_id).then(res => {
        this.seller = res.result.seller_info
      }).catch(function (error) {
        Toast(error.message)
      })
    }
  },
  computed: {
    isAddMode () {
      let mode = this.$route.query.action
      if (mode === 'add') {
        return true
      } else {
        return false
      }
    },
    getTitle () {
      if (this.isAddMode) {
        return '新增账户'
      } else {
        return '修改账户'
      }
    },
    sellerGroupList () {
      let sellerGroupList = [
        {
          flex: 1,
          values: this.sellergroup_list,
          className: 'slot1',
          textAlign: 'center'
        }
      ]
      return sellerGroupList
    }

  },
  methods: {
    // 确认事件
    handleConfirm () {
      this.seller.sellergroup_id = this.$refs.picker.getValues()[0]['sellergroup_id']
      this.seller.sellergroup_name = this.$refs.picker.getValues()[0]['sellergroup_name']
      this.popupVisible = false
    },
    // 选择事件
    onSellerGroupChange (picker, values) {
      // this.seller.sellergroup_id = values[0]
    },
    onDelete () {
      MessageBox.confirm('确定要取消该账号吗？').then(action => {
        delSellerAccount(this.seller.seller_id).then(
          (response) => {
            this.$router.push({ name: 'SellerAccountList' })
          }, (error) => {
            Toast(error.message)
          })
      })
    },
    submit () {
      if (this.isAddMode) {
        Indicator.open()
        addSellerAccount(this.seller).then(
          (response) => {
            Indicator.close()
            Toast(response.message)
            this.$router.push({ name: 'SellerAccountList' })
          }, (error) => {
            Indicator.close()
            Toast(error.message)
          })
      } else {
        Indicator.open()
        editSellerAccount(this.seller).then(
          (response) => {
            Indicator.close()
            Toast(response.message)
            // this.$router.push({ name: 'SellerAccountList' })
          }, (error) => {
            Indicator.close()
            Toast(error.message)
          })
      }
    }
  }
}
</script>

<style scoped>

</style>
