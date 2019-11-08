<template>
    <div class="common-goods-list">
        <div class="ui-goodslist-filter">
            <ul class="filter-list">
                <li class="item"
                    v-for='(item, index) in sortkey'
                    v-bind:key='item.id'
                    v-on:click='setActiveSortkey(item, index)'
                    v-bind:class="{'sortactive': item.id == currentSortKey.id, 'sortnormal' : item.id != currentSortKey.id}">
                    <a v-if='!item.isMore'>{{item.name}}</a>
                    <a v-else>{{sort.name}}</a>
                    <span class="iconfont" :class="isShowMore?'active':''" v-if="item.isMore">&#xe6ea;</span>
                </li>
                <li class="item" @click="showAttribute=true" v-if="attribute.attr_array.length!=0 || attribute.brand_array.length!=0">
                    <a>筛选</a>
                </li>
            </ul>
            <div class="sort-model" v-if='isShowMore' >
                <div v-for='(item, index) in childSort' v-bind:key='item.id' v-on:click='getSortChild(item)' v-bind:class="{'active': item.id == sort.id}">
                    <a>{{item.name}}</a>
                    <span class="iconfont" v-if="item.id == sort.id">&#xe69b;</span>
                </div>
            </div>
        </div>
        <div class="goodslist-body show-goods-list" >
            <!-- 无限加载滚动列表 -->
            <div class="flex-wrapper" v-infinite-scroll="getMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
                <div class="ui-product-body"
                     v-for='(item, index) in goodsList'
                     v-bind:key='index'
                >
                    <div class="list" v-on:click='goDetail(item.goods_id)'>
                        <div class="ui-image-wrapper">
                            <img class="product-img" v-lazy="item.goods_image_url">
                        </div>
                        <div class="flex-right">
                            <div class="product-header">
                                <h3 class="title clear-bottom" style="-webkit-box-orient:vertical">{{ item.goods_name }}</h3>
                            </div>
                            <div class="p-price" style="-webkit-box-orient:vertical">
                                ¥{{ item.goods_price }}
                            </div>
                            <div class="p-info">
                                <span class="platform_store_wrapper"><span v-if="item.is_platform_store" class="platform_store">自营</span></span>
                                <span class="goods_salenum">销量:{{ item.goods_salenum }}</span>
                            </div>
                            <div class="add-cart"><i class="iconfont">&#xe681;</i></div>
                        </div>
                    </div>
                </div>
                <div class="loading-wrapper" v-if="goodsList.length > 0">
                    <p class="common-no-more" v-if='!isMore'>没有更多了</p>
                    <mt-spinner type="triple-bounce" color='#e93b3d' v-if='isMore'></mt-spinner>
                </div>
                <empty-record v-if='goodsList.length <= 0 && !isMore'></empty-record>
            </div>
        </div>

        <!--属性筛选Begin-->
        <mt-popup v-model="showAttribute" position="right" class="common-popup-wrapper" style="width:80%">
            <div class="filter-attribute">
                {{attribute.attr_array.length}}{{attribute.brand_array.length}}
                <dl v-if="attribute.brand_array.length!=0">
                    <dt>品牌</dt>
                    <dd class="clearfix">
                        <a v-for="(item, index) in attribute.brand_array" :class="{'selected':params.b_id==item.brand_id}" @click="checkBrand(item)">{{item.brand_name}}</a>
                    </dd>
                </dl>
                <dl v-for="(attr_info, attr_info_key, attr_info_index) in attribute.attr_array" :key="attr_info_key">
                    <dt>
                        {{attr_info.name}}
                    </dt>
                    <dd class="clearfix">
                        <a v-for="item in attr_info.value" :class="{'selected':checked_attribute[attr_info_index]==item.attrvalue_id}"  @click="checkAttribute(attr_info_index,item)">{{item.attrvalue_name}}</a>
                    </dd>
                </dl>
            </div>
            <mt-button type="primary" size="large" class="ds-button-large mt-10" @click="confirmAttribute()">确定</mt-button>
            <mt-button type="default" size="large" class="ds-button-large mt-10"  @click="cancelAttribute()">重置</mt-button>
        </mt-popup>
        <!--属性筛选End-->

    </div>
</template>

<script>
import EmptyRecord from '../../../components/EmptyRecord'
import { mapState } from 'vuex'
import { searchGoodsList, getAttribute } from '../../../api/homesearch'
export default {
  name: 'CommonGoodsList',
  props: [],
  components: {
    EmptyRecord
  },
  data () {
    return {
      sortkey: [
        {
          key: '',
          order: '',
          name: '综合排序',
          isMore: true,
          id: 0,
          child: [
            {
              key: '',
              order: '',
              name: '综合排序',
              isMore: false,
              id: 3
            },
            {
              key: 'goods_click',
              order: '',
              name: '人气最高',
              isMore: false,
              id: 4
            },
            {
              key: 'goods_price',
              order: '',
              name: '价格高到低',
              isMore: false,
              id: 5
            },
            {
              key: 'goods_price',
              order: 'asc',
              name: '价格低到高',
              isMore: false,
              id: 6
            }
          ]
        },
        {
          key: 'goods_salenum',
          order: '',
          name: '销量排序',
          isMore: false,
          id: 1
        },
        {
          key: 'goods_addtime',
          order: '',
          name: '新品',
          isMore: false,
          id: 2
        }
      ], // 排序数据
      currentSortKey: {}, // 当前选中的排序
      childSort: [], // 综合筛选
      sort: {}, // 综合筛选子集
      isShowMore: false, // 是否显示筛选模态框
      params: {
        b_id: this.$route.query.b_id ? this.$route.query.b_id : '',
        cate_id: this.$route.query.cate_id ? this.$route.query.cate_id : '',
        a_id: this.$route.query.a_id ? this.$route.query.a_id : '', // 规格属性
        is_exchange: 0,
        is_hot: 0,
        activity: null,
        sort_key: this.$route.query.sort_key ? this.$route.query.sort_key : '', // 排序键
        sort_order: this.$route.query.sort_order ? this.$route.query.sort_order : '', // 排序键, //排序值
        page: 0,
        keyword: this.$route.query.keywords ? this.$route.query.keywords : '',
        xianshi: this.$route.query.xianshi ? this.$route.query.xianshi : ''
      },
      showAttribute: false, // 是否选择筛选弹出框
      // 当前商品分类所对应的属性
      attribute: {
        brand_array: [],
        attr_array: []
      },
      checked_attribute: [], // 当前已选择的属性对应的值
      goodsList: [], // 商品列表
      loading: false, // 是否加载更多
      isMore: true // 是否有更多
    }
  },
  computed: {
    ...mapState({

    })
  },
  created () {
    this.currentSortKey = this.sortkey[0]
    this.childSort = this.currentSortKey.child
    this.sort = this.childSort[0]
    // 获取属性参数
    getAttribute(this.params).then(res => {
      this.attribute = res.result
    })
  },
  methods: {
    // 品牌筛选
    checkBrand (item) {
      this.params.b_id = item.brand_id
    },
    // 属性筛选
    checkAttribute (index, item) {
      this.checked_attribute.splice(index, 1, item.attrvalue_id)
      this.params.a_id = this.checked_attribute.join('_')
    },
    confirmAttribute () {
      this.showAttribute = false
      this.getGoodsList()
    },
    cancelAttribute () {
      this.checked_attribute = []
      this.params.a_id = ''
      this.params.b_id = ''
    },
    // closeFiler: 关闭下拉筛选模态框
    closeFiler () {
      this.isShowMore = false
    },
    // isShowDroupMenu: 点击显示下拉框， 并且显示模态框
    isShowDroupMenu () {
      let item = this.currentSortKey
      if (item.isMore) {
        this.isShowMore = true
      } else {
        this.isShowMore = false
      }
    },
    /**
             *  setActiveSortkey: 点击切换数据并设置选中的样式
             *  @param: item 当前选中的item
             */
    setActiveSortkey (item, index) {
      this.currentSortKey = item
      if (item.isMore) {
        this.isShowMore = !this.isShowMore
      } else {
        this.closeFiler()
        this.getValue()
      }
    },
    /**
             *  getValue: 向父级组件发送改变列表事件， 并传递当前的sort_key， sort_order
             */
    getValue () {
      let data = this.getSortValue()
      let res = data
      this.params.page = 1
      this.goodsList = []
      this.loading = false
      this.setParamsByData(res)
      this.getGoodsList()
    },
    /**
             *  getSortValue: 获取排序值
             */
    getSortValue () {
      let sort = this.currentSortKey
      let value = { 'sort_key': '', 'sort_order': '' }
      if (sort.isMore) {
        value.sort_key = this.sort.key
        value.sort_order = this.sort.order
      } else {
        value.sort_key = sort.key
        value.sort_order = sort.order
      }
      return value
    },
    /**
       *  getSortChild: 获取综合筛选的子集， 关闭父级的阴影模态框， 关闭子集， 获取列表数据
       *  @param: item 模态框的item
       */
    getSortChild (item) {
      this.sort = item
      this.isShowMore = !this.isShowMore
      this.getValue()
    },
    /*
       * getMore: 无限滚动加载
       */
    getMore () {
      this.loading = true
      this.params.page = ++this.params.page
      if (this.isMore) {
        this.loading = false
        this.getGoodsList(true)
      }
    },
    /**
       * getGoodsList: 获取商品列表
       * @param：  ispush ？ true ：false 是否需要向商品列表追加数据
       */
    getGoodsList (ispush) {
      searchGoodsList(
        this.params
      ).then(res => {
        this.buildData(ispush, res)
      })
    },
    /**
       * getList: 构建数据
       * @param: ispush 是否改变向元数据追加数据
       * @param: res 接口请求返回的数据
       */
    buildData (ispush, res) {
      if (res) {
        if (ispush) {
          this.goodsList = this.goodsList.concat(res.result.goods_list)
        } else {
          this.goodsList = res.result.goods_list
        }
        this.isMore = res.result.hasmore
      }
    },
    /**
       * 根据事件传递的值来对请求列表重新赋值
       * @param data 事件传递的参数
       */
    setParamsByData (data) {
      let params = this.params
      for (let item in params) {
        for (let list in data) {
          if (item === list) {
            params[item] = data[list]
          }
        }
      }
      return params
    },
    goDetail (goods_id) {
      this.$router.push({ 'name': 'HomeGoodsdetail', 'query': { 'goods_id': goods_id } })
    }
  }
}
</script>

<style lang='scss' scoped>
    .ui-goodslist-filter {
        width: auto;
        ul.filter-list{
            display: flex;
            width: auto;
            justify-content: space-around;
            align-content: center;
            align-items: center;
            border: 0;
            border-top: .045rem solid #E8EAED;
            border-bottom: .045rem solid #E8EAED;
            li{
                font-size: .7rem;
                color: #333;
                border-bottom: .1rem solid transparent;
                position: relative;
                flex-basis: 4.54rem;
                text-align: center;
                height: 1.9rem;
                padding: 0;
                line-height: 1.9rem;
                a {
                    height: 1.9rem;
                    display: inline-block;
                }
                img {
                    height: .18rem;
                    width: .36rem;
                    vertical-align: middle;
                }
                .iconfont{display: inline-block}
            }
            li.sortactive {
                border-bottom-color:#e93b3d;
                a {
                    color:$mainColor;
                }
                .iconfont{color:#e93b3d;}
                .iconfont.active{transform: rotate(180deg);}
            }
            li.sortnormal {
                border-bottom-color: transparent;
                a {
                    color: #333;
                }
            }
            .arrow-icon {
                width: .54rem;
                height: .54rem;
            }
        }
        .sort-model {
            position: absolute;
            left: 0;
            width: 100%;
            z-index: 10;
            div {
                color: #333;
                padding: .68rem;
                font-size: .59rem;
                background-color: #fff;
                margin: 0;
                border-bottom: .045rem solid #E8EAED;
                cursor: pointer;
                display: flex;
                width: auto;
                justify-content: space-between;
                align-content: center;
                align-items: center;
                img {
                    float: right;
                    width: .7rem;
                    height: .7rem;
                }
                &.active {
                    color: $mainColor;
                }
            }
        }
    }
    .ui-product-body {
        border-bottom: .045rem solid rgba(232,234,237,1);
        .list {
            display: flex;
            width: auto;
            align-items: center;
            justify-content: space-between;
            margin: .5rem .45rem;
            position: relative;
            div.ui-image-wrapper {
                width: 5rem;
                height: 5rem;
                position: relative;
                display: flex;
                justify-content: center;
                align-content: center;
                align-items: center;
                flex-basis: 5rem;
                flex-shrink: 0;
                background-position:center center!important;
                background-size:4.54rem 4.54rem;
                background-repeat:no-repeat;
                -webkit-background-size: cover;
                -moz-background-size: cover;
                -o-background-size: cover;
                background-size: cover;

                img.product-img{
                    width: 5rem;
                    height: 5rem;
                    flex-basis: 5rem;
                    flex-shrink: 0;
                }
                img.product-img[lazy=loading] {
                    width: 1.36rem;
                    height: 1.36rem;
                }
                img.product-im[lazy=error] {
                    width: 1.36rem;
                    height: 1.36rem;
                }
                img.product-img[lazy=loaded] {
                    width: 5rem;
                    height: 5rem;
                    flex-basis: 5rem;
                    flex-shrink: 0;
                    background:rgba(255,255,255,1);
                }
                span {
                    position: absolute;
                    height:.9rem;
                    background:rgba(243,244,245,1);
                    line-height: .9rem;
                    text-align: center;
                    font-size:.63rem;
                    color:$mainColor;
                    width: 5rem;
                    bottom: 0;
                    left: 0;
                }
            }
            .flex-right {
                padding-left: .63rem;
                width: 100%;
                position:relative;
                .title {
                    color: #333;
                    font-size: .6rem;
                    line-height:1rem;
                    height:2rem;
                    font-weight: normal;
                    overflow: hidden;
                    margin-bottom: .36rem;
                    &.clear-bottom {
                        margin-bottom: 0;
                    }
                }
                .product-header {
                    margin-bottom: .5rem;
                    display: flex;
                    align-items: center;
                }
                .p-price {
                    color: #e93b3d;
                    font-size:0.7rem;
                }
                .p-info {
                    margin-bottom: .36rem;
                    .platform_store_wrapper{width:2rem;display:inline-block;}
                    .platform_store{font-size:.5rem;color: #e93b3d;border: .045rem solid #e93b3d;border-radius: .13rem;padding:0 .2rem;}
                    .goods_salenum{font-size:.5rem;margin-left:2rem;color:#999;}
                }
                .add-cart{
                    background: #e93b3d;border-radius: 50%;text-align:center;position: absolute;right: .8rem;bottom:.2rem;width:1.4rem;height:1.4rem;line-height:1.4rem;
                    i{font-size:0.8rem;color:#fff;display:block;}
                }
            }
        }
    }

    .filter-attribute{padding:0.5rem;}
    .filter-attribute dl{width:100%;}
    .filter-attribute dt{height:1.2rem;width:100%;overflow: hidden;font-size:0.8rem;color: #333;}
    .filter-attribute dd{}
    .filter-attribute dd a{position: relative;display:inline-block;color: #666;font-size:0.6rem;text-align:center;background-color: #f7f7f7;border-radius:0.2rem;height:1.5rem;line-height:1.5rem;margin:0.5rem 1.5%;width:30%;}
    .filter-attribute dd a.selected{color: #e93b3d;background-color: #fdf0f0;}
    .filter-attribute dd a.selected::after{content: "";position: absolute;right: 0;bottom: 0;width: 15px;height: 15px;border-radius: 0 0 2px 0;background: url("../../../assets/image/home/attribute_selected.png") no-repeat;background-size: 15px auto;overflow: hidden;z-index: 1;}

</style>
