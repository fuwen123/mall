import { requestApi } from '../util/network'

// 热门关键词
export const searchKeywordList = () => requestApi('/Index/search_key_list', 'POST', {})

// 商品搜索
export const searchGoodsList = params =>
  requestApi('/Goods/goods_list', 'GET', {
    keyword: params.keyword ? params.keyword : '', // 关键字 (选填)
    b_id: params.b_id ? params.b_id : '', // 所属品牌id (选填)
    cate_id: params.cate_id ? params.cate_id : '', // 所属商品分类id (选填)
    a_id: params.a_id ? params.a_id : '', // 当前所选属性
    price_from: params.price_from ? params.price_from : '',
    price_to: params.price_to ? params.price_to : '',
    own_shop: params.own_shop ? params.own_shop : '',
    gift: params.gift ? params.gift : '',
    area_id: params.area_id ? params.area_id : '',
    groupbuy: params.groupbuy ? params.groupbuy : '',
    xianshi: params.xianshi ? params.xianshi : '',
    virtual: params.virtual ? params.virtual : '',
    sort_key: params.sort_key ? params.sort_key : '',
    sort_order: params.sort_order ? params.sort_order : '',
    page: params.page ? params.page : '1' // 当前第几页
  })
// 获取商品属性
export const getAttribute = params =>
  requestApi('/Goods/get_attribute', 'GET', {
    b_id: params.b_id ? params.b_id : '', // 所属品牌id (选填)
    cate_id: params.cate_id ? params.cate_id : '', // 所属商品分类id (选填)
    a_id: params.a_id ? params.a_id : '' // 当前所选属性
  })
// 获取商品分类
export const getGoodsclassList = () => requestApi('/Goodsclass/index', 'POST', {})

// 商品搜索
export const getStoreList = (brand, category, keyword, lng, lat, sort_key, page, key = '') =>
  requestApi('/Store/store_list', 'POST', {
    brand: brand, // 所属品牌id (选填)
    cate_id: category, // 所属店铺分类id (选填)
    keyword: keyword, // 关键字 (选填)
    longitude: lng,
    latitude: lat,
    sort_key: sort_key, // 键
    page: page, // 当前第几页
    key: key
  })
// 商品搜索
export const getStoreNearbyList = (brand, category, keyword, lng, lat, sort_key, page, key = '') =>
  requestApi('/Shopnearby/index', 'POST', {
    brand: brand, // 所属品牌id (选填)
    storeclass_id: category, // 所属店铺分类id (选填)
    keyword: keyword, // 关键字 (选填)
    longitude: lng,
    latitude: lat,
    sort_key: sort_key, // 键
    page: page, // 当前第几页
    key: key
  })
// 获取品牌列表
export const getBrandList = recommend =>
  requestApi('/Brand/get_list', 'POST', {
    recommend: recommend
  })

// 获取拼团列表
export const getPintuanList = params =>
  requestApi('/Pintuan/index', 'POST', {
    page: params.page // 当前第几页
  })
