<template>
<div class="common-store-list">
  <div class="ui-storelist-filter">
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
  <div class="storelist-body show-store-list" >
    <!-- 无限加载滚动列表 -->
    <div class="flex-wrapper" v-infinite-scroll="getMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
      <div class="ui-product-body"
           v-for='(item, index) in storeList'
           v-bind:key='index'
      >
        <div class="list-wrapper" v-on:click='goDetail(item.store_id)'>
          <div class="list">
            <div class="ui-image-wrapper">
              <img class="product-img" v-lazy="item.store_logo">
            </div>

            <div class="flex-right">
              <div class="product-header">
                <h3 class="title clear-bottom" style="-webkit-box-orient:vertical">{{ item.store_name }}</h3>

              </div>
              <div class="store-info-wrapper">
                <div class="store-info">
                  <div class="collect-info">已有 {{item.store_collect}} 人关注</div>
                  <div class="sub-title" style="-webkit-box-orient:vertical">
                    <span class="common-score-wrapper"><i class="iconfont front" :style="{width:item.store_credit_percent+'%'}"></i><i class="iconfont back"></i></span>
                  </div>
                </div>
                <div class="btn" :class="{'active':item.is_favorate}"><i class="iconfont">&#xe610;</i>关注</div>
              </div>

            </div>
          </div>
          <div class="goods-list" v-if="item.goods_list && item.goods_list.length">
            <div class="goods-item" v-for="(goods,gindex) in item.goods_list" :key="gindex">
              <div class="goods-image"><img :src="goods.goods_image_url"></div>
              <div class="goods-price">￥{{goods.goods_price}}</div>
            </div>
          </div>
        </div>
      </div>
      <div class="loading-wrapper" v-if="storeList.length > 0">
        <p class="common-no-more" v-if='!isMore'>没有更多了</p>
        <mt-spinner type="triple-bounce" color='#e93b3d' v-if='isMore'></mt-spinner>
      </div>
      <empty-record v-if='storeList.length <= 0 && !isMore'></empty-record>
    </div>
  </div>
</div>
</template>

<script>
import { Toast, MessageBox } from 'mint-ui'
import EmptyRecord from '../../../components/EmptyRecord'
import { mapState } from 'vuex'
import { getStoreList,getStoreNearbyList } from '../../../api/homesearch'
export default {
  name: 'CommonStoreList',
  props: [],
  components: {
    EmptyRecord
  },
  data () {
    return {
      sortkey: [
        {
          key: '',
          name: '综合排序',
          isMore: true,
          id: 0,
          child: [
            {
              key: '',
              name: '综合排序',
              isMore: false,
              id: 3
            },
            {
              key: 'distance',
              name: '距离排序',
              isMore: false,
              id: 4
            }
          ]
        },
        {
          key: 'store_sales',
          name: '销量排序',
          isMore: false,
          id: 1
        },
        {
          key: 'store_credit',
          name: '评分最高',
          isMore: false,
          id: 2
        }
      ], // 排序数据
      currentSortKey: {}, // 当前选中的排序
      childSort: [], // 综合筛选
      sort: {}, // 综合筛选子集
      isShowMore: false, // 是否显示筛选模态框
      params: {
        brand: this.$route.query.brand ? this.$route.query.brand : '',
        category: this.$route.query.storeclass_id ? this.$route.query.storeclass_id : '',
        is_exchange: 0,
        is_hot: 0,
        activity: null, // TODO: 确认activity的值
        sort_key: this.$route.query.sort_key ? this.$route.query.sort_key : '',
        sort_value: 2,
        page: 0,
        keyword: this.$route.query.keywords ? this.$route.query.keywords : ''
      },
      storeList: [], // 商品列表
      loading: false, // 是否加载更多
      isMore: true // 是否有更多
    }
  },
  computed: {
    ...mapState({
      member_point: state => state.member.point,
      token: state => state.member.token,
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
     *  getValue: 向父级组件发送改变列表事件， 并传递当前的sort_key， sort_value
     */
    getValue () {
      let data = this.getSortValue()
      let res = data
      this.params.page = 1
      this.storeList = []
      this.loading = false
      this.setParamsByData(res)
      if(data.sort_key=='distance'){
        if(!this.member_point){
        MessageBox.confirm('我们需要先获取您的位置信息', '', { cancelButtonText: '拒绝', confirmButtonText: '同意' }).then(action => {
            this.$router.push({name:'HomeMap'})
          }).catch(function (error) {
            
          })
        
        }else{
        this.getStoreList(false,true)
        }
        
      }else{
        this.getStoreList()
      }
    },
    /**
     *  getSortValue: 获取排序值
     */
    getSortValue () {
      let sort = this.currentSortKey
      let value = { 'sort_key': '', 'sort_value': '' }
      if (sort.isMore) {
        value.sort_key = this.sort.key
      } else {
        value.sort_key = sort.key
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
        this.getStoreList(true)
      }
    },
    /**
     * getStoreList: 获取商品列表
     * @param：  ispush ？ true ：false 是否需要向商品列表追加数据
     */
    getStoreList (ispush=false,nearby=false) {
      let data = this.params
      if(nearby){
      getStoreNearbyList(
        data.brand,
        data.category,
        data.keyword,
        this.member_point.lng,
        this.member_point.lat,
        data.sort_key,
        data.page,
        this.token
      ).then(res => {
        this.buildData(ispush, res)
      })
      }else{
      getStoreList(
        data.brand,
        data.category,
        data.keyword,
        0,
        0,
        data.sort_key,
        data.page,
        this.token
      ).then(res => {
        this.buildData(ispush, res)
      })
      }
      
    },
    /**
     * getList: 构建数据
     * @param: ispush 是否改变向元数据追加数据
     * @param: res 接口请求返回的数据
     */
    buildData (ispush, res) {
      if (res) {
        if (ispush) {
          this.storeList = this.storeList.concat(res.result.store_list)
        } else {
          this.storeList = res.result.store_list
        }
        this.isMore = res.result.hasmore
      }
    },
    /**
     * 根据事件传递的值来对请求列表重新赋值
     * @param data 事件传递的参数
     * @returns {params|{brand, category, is_exchange, is_hot, activity, sort_key, sort_value, page, per_page, keyword}}
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
    goDetail (store_id) {
      this.$router.push({ 'name': 'HomeStoredetail', 'query': { 'id': store_id } })
    }
  }
}
</script>

<style lang='scss' scoped>
  .ui-storelist-filter {
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
    font-size: 0.7rem;
    color: #333;
    border-bottom: 0.1rem solid transparent;
    position: relative;
    flex-basis: 5rem;
    text-align: center;
    height: 2.1rem;
    padding: 0;
    line-height: 2.1rem;
  a {
    height: 2.1rem;
    display: inline-block;
  }
  img {
    height: 0.2rem;
    width: 0.4rem;
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
    padding: 0.75rem;
    font-size: 0.65rem;
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
    width: 0.8rem;
    height: 0.8rem;
  }
  &.active {
     color: #e93b3d;
   }
  }
  }
  }
  .ui-product-body {

    .goods-list{padding:.8rem .5rem;border-top:1px solid #e1e1e1;overflow: hidden;background:#fff;
      .goods-item{float:left;width:25%;text-align: center;
        .goods-image {margin-bottom:.2rem;
          img{width:3.5rem;height: 3.5rem;}
        }
        .goods-price{color:$primaryColor;font-size:.7rem;white-space: nowrap;overflow: hidden;text-overflow:ellipsis;width:100%}
      }
    }
    .list-wrapper{margin-bottom:.5rem;}
  .list {
    display: flex;
    width: auto;
    align-items: center;
    justify-content: space-between;
    padding:0.5rem;
    position: relative;
    background:#fff;

  div.ui-image-wrapper {
    width: 3rem;
    height: 3rem;
    position: relative;
    border:1px solid #e1e1e1;
    display: flex;
    justify-content: center;
    align-content: center;
    align-items: center;
    flex-basis: 3rem;
    flex-shrink: 0;

    background-position:center center!important;
    background-size:5rem 5rem;
    background-repeat:no-repeat;
    -webkit-background-size: cover;
    -moz-background-size: cover;
    -o-background-size: cover;
    background-size: cover;

  img.product-img{
    width: 3rem;
    height: 3rem;
    flex-basis: 3rem;
    flex-shrink: 0;
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
    padding-top:.5rem;
    padding-left: 0.7rem;
    width: 100%;
    position:relative;
  .title {
    color: #333;
    font-size: 0.75rem;
    font-weight: normal;

    display:-moz-box;
    display:-webkit-box;

    -webkit-line-clamp: 2;
    -moz-line-clamp: 2;

    -moz-box-orient:vertical;
    -webkit-box-orient:vertical;
    box-orient:vertical;

    overflow: hidden;
    margin-bottom: 0.4rem;
  &.clear-bottom {
     margin-bottom: 0;
   }
  }
.store-info-wrapper{display: flex;
  .store-info{flex:1}
  .btn{width:3rem;font-size:.7rem;color:#e1e1e1;text-align: center;border:1px solid #e1e1e1;border-radius:1rem;height: 1rem;line-height: 1rem;}
  .btn.active{border-color:$primaryColor;background:$primaryColor;color:#fff}
}
  .product-header {
    display: flex;
    align-items: center;
  }
    .collect-info{font-size:.6rem;color:#999}
  .sub-title {
    color: #55595F;
    font-size: .7rem;

    display:-moz-box;
    display:-webkit-box;

    -webkit-line-clamp: 1;
    -moz-line-clamp: 1;

    -moz-box-orient:vertical;
    -webkit-box-orient:vertical;
    box-orient:vertical;

    overflow: hidden;
    margin-bottom: 0.4rem;
  }
  .price {
    margin-bottom: 0.4rem;
  span {
  &:first-child {
     color: #e93b3d;
     font-size: 0.8rem;
   }
  &:last-child {
     color: #A4AAB3;
     font-size: 0.6rem;
     text-decoration: line-through;
   }
  }
  }
  .sendway {
    display: flex;
    justify-content: space-between;
    align-items: center;
    font-size: 0.6rem;
    font-weight: 'Regular';
  span{
    color: #7C7F88;
  &.self-support {
     font-size: 0.5rem;
     color: #e93b3d;
     border: 1px solid #e93b3d;
     border-radius: 0.1rem;
     width: 1.6rem;
     height: 0.8rem;
     line-height: 0.8rem;
     text-align: center;
   }
  }
  }
  }
  }
  }
</style>
