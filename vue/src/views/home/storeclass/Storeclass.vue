<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header class="common-header" title="店铺分类">
                <mt-button icon="back" slot="left" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div class="class-list">
            <ul>
                <li v-for="item in storeclass_list" v-bind:key="item.storeclass_id" @click="goHomeStorelist(item.storeclass_id)">
                    {{item.storeclass_name}}
                </li>
            </ul>
        </div>
        <empty-record v-if="storeclass_list && !storeclass_list.length"></empty-record>
    </div>
</template>

<script>
import { getStoreClass } from '../../../api/seller'
import EmptyRecord from '../../../components/EmptyRecord'
import { Toast, Indicator } from 'mint-ui'
export default {
  components: {
    EmptyRecord
  },
  name: 'Storeclass',
  data () {
    return {
      storeclass_list: false
    }
  },
  created: function () {
    this.getStoreclassList()
  },
  methods: {
    getStoreclassList () {
      Indicator.open()
        getStoreClass().then(res => {
        Indicator.close()
        this.storeclass_list = res.result.store_class
      }).catch(function (error) {
        Indicator.close()
        Toast(error.message)
      })
    },
    goHomeStorelist (storeclassId) {
      this.$router.push({ name: 'HomeStorelist', query: { storeclass_id: storeclassId } })
    }
  }
}
</script>

<style scoped>
.class-list{}
.class-list li{box-sizing: border-box;
    text-align: center;
    padding:.4rem;
    width: 50%;
    font-size:0.7rem;
    float: left;
    background:#fff;
    line-height:2rem;
    border-bottom: 1px solid #dedbdb;
    border-right: 1px solid #dedbdb;}
</style>
