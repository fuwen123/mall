<template>
    <div class="container">
        <div class="common-header-wrap">
            <mt-header class="common-header" title="拼团活动">
                <mt-button icon="back" slot="left" @click="$router.go(-1)"></mt-button>
            </mt-header>
        </div>


        <div class="goodslist-body show-goods-list" >
            <!-- 无限加载滚动列表 -->
            <div class="flex-wrapper" v-infinite-scroll="loadMore" infinite-scroll-disabled="loading" infinite-scroll-distance="10">
                <div class="ui-product-body"
                     v-for='(item, index) in pintuanList'
                     v-bind:key='index'
                >
                    <div class="list" v-on:click='goDetail(item)'>
                        <div class="ui-image-wrapper">
                            <img class="product-img" v-lazy="item.pintuan_image">
                        </div>
                        <div class="flex-right">
                            <div class="product-header">
                                <h3 class="title clear-bottom" style="-webkit-box-orient:vertical">{{ item.pintuan_name }}</h3>
                            </div>
                            <div class="p-price" style="-webkit-box-orient:vertical">
                                <span class="strong">{{ item.pintuan_zhe }}折</span>成团折扣
                            </div>
                            <div class="p-info">
                                <span class="pintuan_limit_number">原价：￥{{item.pintuan_goods_price}}</span>
                            </div>
                            <span class="btn">我要开团</span>
                        </div>
                    </div>
                </div>
                <div class="loading-wrapper" v-if="pintuanList.length > 0">
                    <p class="common-no-more" v-if='!isMore'>没有更多了</p>
                    <mt-spinner type="triple-bounce" color='#e93b3d' v-if='isMore'></mt-spinner>
                </div>
                <empty-record v-if='pintuanList.length <= 0 && !isMore'></empty-record>
            </div>
        </div>



    </div>
</template>

<script>
    import EmptyRecord from '../../../components/EmptyRecord'
    import { Toast, Indicator } from 'mint-ui'
    import { getPintuanList } from '../../../api/homesearch'
    export default {
        name: "PintuanList",
        components: {
            EmptyRecord
        },
        data () {
            return {
                pintuanList: [],
                params: { 'page': 0, 'per_page': 10 },
                loading: false, // 是否加载更多
                isMore: true, // 是否有更多
            }
        },
        methods: {
            loadMore () {
                this.loading = true
                this.params.page = ++this.params.page
                if (this.isMore) {
                    this.loading = false
                    this.getPintuanList(true)
                }
            },
            getPintuanList () {
                Indicator.open()
                getPintuanList(this.params).then(res => {
                    Indicator.close()
                    if (res.result.hasmore) {
                        this.isMore = true
                    } else {
                        this.isMore = false
                    }

                    if (this.pintuanList) {
                        this.pintuanList = this.pintuanList.concat(res.result.pintuan_list)
                    } else {
                        this.pintuanList = res.result.pintuan_list
                    }

                }).catch(function (error) {
                    Indicator.close()
                    Toast(error.message)
                })

            },
            goDetail (item) {
                this.$router.push({ 'name': 'HomeGoodsdetail', 'query': { 'goods_id': item.pintuan_goods_id } })
            }
        }
    }
</script>

<style scoped lang='scss'>
    .ui-product-body {
        background:#fff;
        border-bottom: 1px solid rgba(232,234,237,1);
    .list {
        display: flex;
        width: auto;
        align-items: center;
        justify-content: space-between;
        padding:0.5rem;
        position: relative;
    div.ui-image-wrapper {
        border:1px solid #e1e1e1;
        width: 5.5rem;
        height: 5.5rem;
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
        width:5.5rem;
        height:5.5rem;
        flex-basis:5.5rem;
        flex-shrink: 0;
    }
    img.product-img[lazy=loading] {
        width:1.5rem;
        height:1.5rem;
    }
    img.product-im[lazy=error] {
        width:1.5rem;
        height:1.5rem;
    }
    img.product-img[lazy=loaded] {
        width:5.5rem;
        height:5.5rem;
        flex-basis:5.5rem;
        flex-shrink: 0;
        background:rgba(255,255,255,1);
    }
    span {
        position: absolute;
        height:1rem;
        background:rgba(243,244,245,1);
        line-height:1rem;
        text-align: center;
        font-size:0.7rem;
        color:#e93b3d;
        width:5.5rem;
        bottom: 0;
        left: 0;
    }
    }
    .flex-right {
        padding-left:0.7rem;
        flex: 1;
        overflow: hidden;
        position:relative;
        height: 5.5rem;
    .title {
        color: #333;
        font-size: 0.8rem;
        font-weight: normal;
        white-space: nowrap;
        text-overflow:ellipsis;
        overflow: hidden;
        margin-bottom:0.4rem;
    &.clear-bottom {
         margin-bottom: 0;
     }
    }
    .product-header {
        margin-bottom:.4rem;
        display: flex;
        align-items: center;
    }
    .p-price {font-size:.6rem;color:#999;
        .strong{
            color: #e93b3d;
            font-size:.8rem;
            line-height:.8rem;
            margin-right:.2rem;
        }
    }
    .p-info {
        margin-bottom:0.4rem;
    .pintuan_limit_number{font-size:.6rem;color:#999;}
    }
        .btn{border-radius: .2rem;color: #fff;font-size:.7rem;padding:.2rem .5rem;line-heigh:1rem;background: #e93b3d;border: 1px solid #e93b3d;}
    }
    }
    }
</style>
