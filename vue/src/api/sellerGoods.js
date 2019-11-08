import { requestApi } from '../util/network'

// 获取卖家商品列表
export const getGoodsList = (params, keyword, goods_type, search_type) =>
  requestApi(
    '/Sellergoods/goods_list',
    'POST',
    {
      keyword: keyword,
      goods_type: goods_type,
      search_type: search_type,
      page: params.page,
      per_page: params.per_page,
      client_type: 'wap'
    },
    'seller'
  )
// 删除商品
export const dropGoods = commonid =>
  requestApi(
    '/Sellergoods/drop_goods',
    'POST',
    {
      commonid: commonid,
      client_type: 'wap'
    },
    'seller'
  )
// 商品上架
export const goodsShow = commonid =>
  requestApi(
    '/Sellergoods/goods_show',
    'POST',
    {
      commonid: commonid,
      client_type: 'wap'
    },
    'seller'
  )
// 商品下架
export const goodsUnshow = commonid =>
  requestApi(
    '/Sellergoods/goods_unshow',
    'POST',
    {
      commonid: commonid,
      client_type: 'wap'
    },
    'seller'
  )
