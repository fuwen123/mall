<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header class="common-header" title="品牌街">
                <mt-button icon="back" slot="left" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>
        <div class="brand-list">
            <h2 v-if="recommend_brand && recommend_brand.length">推荐品牌</h2>
            <ul v-if="recommend_brand && recommend_brand.length">
                <li v-for="item in recommend_brand" v-bind:key="item.brand_id" @click="goHomeGoodslist(item.brand_id)">
                    <div class="p-img" :style="{backgroundImage:'url('+item.brand_pic+')'}"></div>
                </li>
            </ul>
            <div v-for="(bclass,index) in brand_class" :key="index">
            <h2>{{bclass.brand_class}}</h2>
            <ul>
                <li v-for="item in brand_list[index]" v-bind:key="item.brand_id" @click="goHomeGoodslist(item.brand_id)">
                    <div class="p-img" :style="{backgroundImage:'url('+item.brand_pic+')'}"></div>
                </li>
            </ul>
            </div>
        </div>
    </div>
</template>

<script>
import { getBrandList } from '../../../api/homesearch'
import EmptyRecord from '../../../components/EmptyRecord'
import { Toast, Indicator } from 'mint-ui'
export default {
  components: {
    EmptyRecord
  },
  name: 'Brand',
  data () {
    return {
      brand_list: false,
      recommend_brand: false,
        brand_class:false,
    }
  },
  created: function () {
    this.getBrandList()
  },
  methods: {
    getBrandList () {
      Indicator.open()
      getBrandList(0).then(res => {
        Indicator.close()
        this.brand_list = res.result.brand_c
          this.recommend_brand = res.result.brand_r
          this.brand_class = res.result.brand_class
      }).catch(function (error) {
        Indicator.close()
        Toast(error.message)
      })


    },
    goHomeGoodslist (brandId) {
      this.$router.push({ name: 'HomeGoodslist', query: { b_id: brandId } })
    }
  }
}
</script>

<style scoped lang="scss">
.container{background:#fff}
.brand-list{
    h2{line-height:2rem;font-size:.7rem;padding-left:.5rem;font-weight:700;}
    ul{overflow: hidden;border-top:1px solid #f6f6f9}
    li{
        box-sizing: border-box;
        text-align: center;
        width: 25%;
        padding-bottom:20%;
        float: left;
        border-bottom: 1px solid #f6f6f9;
        border-right: 1px solid #f6f6f9;
        position:relative;
        .p-img{
            position:absolute;
            top:.5rem;
            bottom:.5rem;
            right:.5rem;
            left:.5rem;
            background-size:contain;
            background-position:center;
            background-repeat:no-repeat;
        }

    }
    li:nth-child(4n) {
        border-right: 0;
    }
}

</style>
