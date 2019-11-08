<template>
  <div class="container">
    <div  v-for='(editablePageConfig,key) in editablePageConfigList' :key="key">
      <model1 v-if='editablePageConfig.editable_page_model_id==1' v-bind:editablePageConfig="editablePageConfig"></model1>
      <model2 v-if='editablePageConfig.editable_page_model_id==2' v-bind:editablePageConfig="editablePageConfig"></model2>
      <model6 v-if='editablePageConfig.editable_page_model_id==6' v-bind:editablePageConfig="editablePageConfig"></model6>
      <model12 v-if='editablePageConfig.editable_page_model_id==12' v-bind:editablePageConfig="editablePageConfig"></model12>
      <model13 v-if='editablePageConfig.editable_page_model_id==13' v-bind:editablePageConfig="editablePageConfig"></model13>
      <model14 v-if='editablePageConfig.editable_page_model_id==14' v-bind:editablePageConfig="editablePageConfig"></model14>
      <model15 v-if='editablePageConfig.editable_page_model_id==15' v-bind:editablePageConfig="editablePageConfig"></model15>
    </div>
  </div>
</template>

<script>
import { Toast } from 'mint-ui'
import { getEditablePageConfigList } from '../../../api/homeindex'
import Model1 from './editable_page_model/Model1'
import Model2 from './editable_page_model/Model2'
import Model6 from './editable_page_model/Model6'
import Model12 from './editable_page_model/Model12'
import Model13 from './editable_page_model/Model13'
import Model14 from './editable_page_model/Model14'
import Model15 from './editable_page_model/Model15'
export default {
  data () {
    return {
      editablePage:{},
      editablePageConfigList: []
    }
  },
  components: {
    Model1,
    Model2,
    Model6,
    Model12,
    Model13,
    Model14,
    Model15
  },
  mounted () {
  },
  created: function () {
    var special_id = this.$route.query.special_id
    if (!special_id) {
      Toast('缺少参数')
      return
    }
    getEditablePageConfigList(special_id).then(res => {

      this.editablePage = res.result.editable_page
      this.editablePageConfigList = res.result.editable_page_config_list
    }).catch(function (error) {
      Toast(error.message)
    })
  },

  methods: {

  }
}
</script>

<style scoped>

</style>
