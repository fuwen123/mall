import { requestApi } from '../util/network'

// 获取当前店铺所有代金券
export const getStoreVoucher = storeId =>
  requestApi('/Voucher/voucher_tpl_list', 'POST', {
    store_id: storeId, // 所属店铺
    gettype: 'points'
  })
// 领取取代金券
export const receiveVoucher = tid =>
  requestApi(
    '/Membervoucher/voucher_point',
    'POST',
    {
      tid: tid
    },
    'member'
  )
// 获取当前店铺信息
export const getStoreInfo = (storeId, token) =>
  requestApi('/Store/store_info', 'POST', {
    store_id: storeId, // 所属店铺
    key: token
  })

// 获取店铺分类
export const getStoreGoodsClass = storeId =>
  requestApi('/Store/store_goods_class', 'POST', {
    store_id: storeId // 所属店铺
  })
// 获取店铺商品
export const getStoreGoodsList = params =>
  requestApi('/Store/store_goods', 'POST', {
    page: params.page,
    per_page: params.per_page,
    storegc_id: params.gc_id,
    keyword: params.keyword,
    store_id: params.store_id, // 所属店铺
    sort_order: params.sort_order,
    sort_key: params.sort_key
  })
