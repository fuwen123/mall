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
                                {{ item.goods_price }}
                            </div>
                            <div class="p-info">
                                <span class="platform_store">自营</span>
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
    </div>
</template>

<script>
import EmptyRecord from '../../../components/EmptyRecord'
import { mapState } from 'vuex'
import { getStoreGoodsList } from '../../../api/homestoredetail'
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
              key: '5',
              order: '',
              name: '综合排序',
              isMore: false,
              id: 3
            },
            {
              key: '4',
              order: '',
              name: '人气最高',
              isMore: false,
              id: 4
            },
            {
              key: '2',
              order: '',
              name: '价格高到低',
              isMore: false,
              id: 5
            },
            {
              key: '2',
              order: '1',
              name: '价格低到高',
              isMore: false,
              id: 6
            }
          ]
        },
        {
          key: '3',
          order: '',
          name: '销量排序',
          isMore: false,
          id: 1
        },
        {
          key: '1',
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
        store_id: this.$route.query.id ? this.$route.query.id : '',
        gc_id: this.$route.query.gc_id ? this.$route.query.gc_id : '',
        is_exchange: 0,
        is_hot: 0,
        activity: null,
        sort_key: this.$route.query.sort_key ? this.$route.query.sort_key : '', // 排序键
        sort_order: this.$route.query.sort_order ? this.$route.query.sort_order : '', // 排序键, //排序值
        page: 0,
        keyword: this.$route.query.keyword ? this.$route.query.keyword : ''
      },
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
  },
  methods: {
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
      this.params=this.setParamsByData(res)
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
      getStoreGoodsList(
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
            border-top: 1px solid #E8EAED;
            border-bottom: 1px solid #E8EAED;
            li{
                font-size:0.7rem;
                color: #333;
                border-bottom:0.1rem solid transparent;
                position: relative;
                flex-basis:5rem;
                text-align: center;
                height:2.1rem;
                padding: 0;
                line-height:2.1rem;
                a {
                    height:2.1rem;
                    display: inline-block;
                }
                img {
                    height:0.2rem;
                    width:0.4rem;
                    vertical-align: middle;
                }
                .iconfont{display: inline-block}
            }
            li.sortactive {
                border-bottom-color:#e93b3d;
                a {
                    color:$primaryColor;
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
                width: 0.6rem;
                height: 0.6rem;
            }
        }
        .sort-model {
            position: absolute;
            left: 0;
            width: 100%;
            z-index: 10;
            div {
                color: #333;
                padding:0.75rem;
                font-size:0.65rem;
                background-color: #fff;
                margin: 0;
                border-bottom: 1px solid #E8EAED;
                cursor: pointer;
                display: flex;
                width: auto;
                justify-content: space-between;
                align-content: center;
                align-items: center;
                img {
                    float: right;
                    width:0.8rem;
                    height:0.8rem;
                }
                &.active {
                    color: #e93b3d;
                }
            }
        }
    }
    .ui-product-body {
        border-bottom: 1px solid rgba(232,234,237,1);
        .list {
            display: flex;
            width: auto;
            align-items: center;
            justify-content: space-between;
            margin:0.55rem 0.5rem;
            position: relative;
            div.ui-image-wrapper {
                width:5.5rem;
                height:5.5rem;
                position: relative;
                display: flex;
                justify-content: center;
                align-content: center;
                align-items: center;
                flex-basis: 5.5rem;
                flex-shrink: 0;
                background-position:center center!important;
                background-size:5rem 5rem;
                background-repeat:no-repeat;
                -webkit-background-size: cover;
                -moz-background-size: cover;
                -o-background-size: cover;
                background-size: cover;
                img.product-img{
                    width: 5.5rem;
                    height: 5.5rem;
                    flex-basis: 5.5rem;
                    flex-shrink: 0;
                }
                img.product-img[lazy=loading] {
                    width: 1.5rem;
                    height: 1.5rem;
                }
                img.product-im[lazy=error] {
                    width: 1.5rem;
                    height: 1.5rem;
                }
                img.product-img[lazy=loaded] {
                    width: 5.5rem;
                    height: 5.5rem;
                    flex-basis: 5.5rem;
                    flex-shrink: 0;
                    background:rgba(255,255,255,1);
                }

                span {
                    position: absolute;
                    height:1rem;
                    background:rgba(243,244,245,1);
                    line-height: 1rem;
                    text-align: center;
                    font-size:0.7rem;
                    color:#e93b3d;
                    width: 5.5rem;
                    bottom: 0;
                    left: 0;
                }
            }
            .flex-right {
                padding-left: 0.7rem;
                width: 100%;
                position:relative;
                .title {
                    color: #333;
                    font-size: 0.8rem;
                    font-weight: normal;
                    display:-moz-box;
                    display:-webkit-box;
                    -webkit-line-clamp: 2;
                    -moz-line-clamp: 2;
                    -moz-box-orient:vertical;
                    -webkit-box-orient:vertical;
                    box-orient:vertical;
                    overflow: hidden;
                    margin-bottom:0.4rem;
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
                    font-size: 1rem;
                }
                .p-info {
                    margin-bottom:0.4rem;
                    .platform_store{font-size: .8rem;color: #e93b3d;border: 1px solid #e93b3d;border-radius: 0.15rem;padding:0 .2rem;}
                    .goods_salenum{font-size:.8rem;margin-left:2rem;color:#999;}
                }
                .add-cart{
                    background: #e93b3d;border-radius: 50%;text-align:center;position: absolute;right: .8rem;bottom:.2rem;width:2rem;height:2rem;line-height:2rem;
                    i{font-size:1rem;color:#fff;}
                }
            }
        }
    }
</style>
