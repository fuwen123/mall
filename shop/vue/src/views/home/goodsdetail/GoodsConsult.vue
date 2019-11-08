<template>
<div class="container">
    <div class="common-header-wrap">
        <mt-header title="商品咨询" class="common-header">
            <mt-button slot="left" icon="back" @click="$router.go(-1)"></mt-button>
        </mt-header>
    </div>
    <div class="consult-form mb-5">
        <div @click="typeVisible=true">
            <mt-cell title="咨询类型" :value="type_name" is-link />
        </div>
        <mt-field label="咨询内容" v-model="content" type="textarea"></mt-field>

        <mt-field label="验证码" v-model="pictureCode">
            <img @click="changePictureCode" height="48" :src="pictureCodeUrl" class="countdown" >
        </mt-field>
        <mt-button class="ds-button-large" type="primary" v-on:click="submit">保存</mt-button>
    </div>
    <mt-popup v-model="typeVisible" position="right" class="common-popup-wrapper">
        <div class="common-header-wrap">
            <mt-header title="咨询类型" class="common-header">
                <mt-button slot="left" icon="back" @click="typeVisible=false"></mt-button>
            </mt-header>
        </div>
        <div class="common-popup-content">
            <mt-radio
                    v-model="type_id"
                    :options="type_options">
            </mt-radio>
        </div>
    </mt-popup>
    <div v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
        <div  v-if="consult_list && consult_list.length">
            <div class="consult-item"  v-for="(item, index) in consult_list">
                <div class="consult-info">

                    <div class="p-info">
                        <div class="explain">{{consultType[item.consulttype_id].consulttype_name}}<span class="right">{{ $moment.unix(item.consult_addtime).format('YYYY年MM月DD日') }}</span></div>
                        <div class="name">{{item.consult_content}}</div>
                        <div class="replay" v-if="item.consult_reply">{{item.consult_reply}}</div>
                    </div>
                </div>
            </div>
        </div>
        <empty-record v-else-if="consult_list && !consult_list.length"></empty-record>
    </div>
</div>
</template>

<script>
import { Toast, Indicator } from 'mint-ui'
import { mapState, mapMutations } from 'vuex'
import EmptyRecord from '../../../components/EmptyRecord'
import { getGoodsConsult, addGoodsConsult } from '../../../api/homegoodsdetail'
import { checkPictureCaptcha } from '../../../api/common'
export default {
  name: 'HomeGoodsConsult',
  data () {
    return {
      goods_id: this.$route.query.goods_id,
      pictureCode: '',
      pictureCodeUrl: '',
      content: '',
      type_name: '',
      typeVisible: false,

      type_id: '',
      type_options: [],

      params: { 'page': 0, 'per_page': 10 },
      consult_list: false,
      pictureCodeValid: false,
      loading: false, // 是否加载更多
      isMore: true // 是否有更多
    }
  },
  components: {
    EmptyRecord
  },
  computed: {
    ...mapState({
      token: state => state.member.token,
      consultType: state => state.goodsdetail.consultType
    })
  },
  mounted () {
    this.changePictureCode()
  },
  created () {
    if (!this.goods_id) {
      Toast('参数错误')
      this.$router.go(-1)
    }
    let temp = this.consultType
    for (var i in temp) {
      if (!this.type_id) {
        this.type_id = temp[i].consulttype_id + ''
      }
      this.type_options.push({
        label: temp[i].consulttype_name,
        value: temp[i].consulttype_id + ''
      })
    }
  },
  watch: {
    pictureCode: function (val) {
      if (val.length >= 4) {
        this.pictureCodeWait = true
        checkPictureCaptcha(val).then(
          response => {
            this.pictureCodeWait = false
            this.pictureCodeValid = true
          },
          error => {
            this.pictureCodeWait = false
            Toast(error.message)
          }
        )
      }
    },
    type_id: function (type_id) {
      this.type_name = this.consultType[type_id].consulttype_name
      this.typeVisible = false
    }

  },
  methods: {
    submit () {
      if (!this.pictureCode) {
        Toast('请先输入图片验证码')
        return
      }
      if (!this.content) {
        Toast('请先输入咨询内容')
        return
      }
      if (!this.pictureCodeValid) {
        Toast('验证码错误')
        return
      }
      addGoodsConsult(this.goods_id, this.type_id, this.content, this.token).then(res => {
        Toast(res.message)
        this.content = ''
        this.pictureCode = ''
        this.params = { 'page': 0, 'per_page': 10 }
        this.consult_list = false
        this.loading = false
        this.isMore = true
        this.loadMore()
      }).catch(function (error) {
        Toast(error.message)
      })
    },
    changePictureCode () {
      this.pictureCodeUrl = process.env.VUE_APP_API_HOST + '/Seccode/makecode?r=' + Math.random()
    },
    loadMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getConsultList(true)
      }
    },
    getConsultList () {
      Indicator.open()

      getGoodsConsult(this.params, this.goods_id).then(res => {
        Indicator.close()
        if (res.result.hasmore) {
          this.isMore = true
        } else {
          this.isMore = false
        }

        let temp = res.result.consult_list
        if (temp) {
          if (!this.consult_list) {
            this.consult_list = temp
          } else {
            this.consult_list = this.consult_list.concat(temp)
          }
        }
      }).catch(function (error) {
        Indicator.close()
        Toast(error.message)
      })
    }
  }

}
</script>

<style scoped lang="scss">
    .consult-item{background:#fff;border-bottom:1px solid #e1e1e1}
    .consult-info{padding:.5rem;display: flex}
    .consult-info .p-info{flex:1;margin-left:1rem;}
    .consult-info .p-info .name{font-size:0.7rem;}
    .consult-info .p-info .explain{font-size:0.7rem;color:$mainColor;margin-bottom:.5rem}
    .consult-info .p-info .explain .right{float:right;color:gray}
    .consult-info .p-info .replay{border-top:1px dashed #e1e1e1;font-size:.8rem;margin-top:.5rem;padding-top:.5rem;color:green}
</style>
