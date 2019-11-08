<template>
    <div class="seller-o2o-index">
        <div class="common-header-wrap">
            <mt-header title="店铺设置" class="common-header">
                <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <mt-field label="主营商品" type="text" v-model="settings.store_mainbusiness" />
        <mt-field label="QQ" type="text" v-model="settings.store_qq" />
        <mt-field label="阿里旺旺" type="text" v-model="settings.store_ww" />
        <mt-field label="SEO关键" type="text" v-model="settings.store_keywords" />
        <mt-field label="SEO店铺" type="text" v-model="settings.store_description" />
        <mt-field label="店铺电话" type="text" v-model="settings.store_phone" />
        <mt-button class="ds-button-large" type="primary" v-on:click="submit">保存</mt-button>
    </div>
</template>

<script>
import { getStoreInfo, editStoreInfo } from '../../../api/sellerSetting'
import { Toast } from 'mint-ui'
export default {
  name: 'SellerSettingInfo',
  data () {
    return {
      settings: {}
    }
  },
  created: function () {
    getStoreInfo().then(res => {
      this.settings = res.result.store_info
    }).catch(function (error) {
      Toast(error.message)
    })
  },
  computed: {

  },
  methods: {
    submit () {
      editStoreInfo(this.settings.store_qq, this.settings.store_ww, this.settings.store_phone, this.settings.store_mainbusiness, this.settings.store_keywords, this.settings.store_description).then(res => {
        Toast(res.message)
      }).catch(function (error) {
        Toast(error.message)
      })
    }

  }
}
</script>
