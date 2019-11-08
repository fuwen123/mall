import { requestApi } from '../util/network'

// 检索可选取的分销商品
export const searchGoodsList = goods_name =>
  requestApi(
    '/Sellerinviter/search_goods',
    'POST',
    {
      goods_name: goods_name,
      client_type: 'wap'
    },
    'seller'
  )
// 获取分销商品列表
export const getInviterGoodsList = () => requestApi('/Sellerinviter/goods_list', 'POST', {}, 'seller')
// 新增分销商品
export const addInviterGoods = data =>
  requestApi(
    '/Sellerinviter/goods_add',
    'POST',
    {
      inviter_goods_commonid: data.goods_commonid,
      inviter_ratio_1: data.inviter_ratio_1,
      inviter_ratio_2: data.inviter_ratio_2,
      inviter_ratio_3: data.inviter_ratio_3
    },
    'seller'
  )
// 编辑分销商品
export const editInviterGoods = data =>
  requestApi(
    '/Sellerinviter/goods_edit',
    'POST',
    {
      inviter_goods_commonid: data.goods_commonid,
      inviter_ratio_1: data.inviter_ratio_1,
      inviter_ratio_2: data.inviter_ratio_2,
      inviter_ratio_3: data.inviter_ratio_3,
      client_type: 'wap'
    },
    'seller'
  )
// 获取单个分销商品
export const getInviterGoodsInfo = goods_commonid =>
  requestApi(
    '/Sellerinviter/goods_info',
    'POST',
    {
      goods_commonid: goods_commonid,
      client_type: 'wap'
    },
    'seller'
  )
// 删除分销商品
export const delInviterGoods = goods_commonid =>
  requestApi(
    '/Sellerinviter/goods_del',
    'POST',
    {
      goods_commonid: goods_commonid,
      client_type: 'wap'
    },
    'seller'
  )

// 获取店铺分销业绩
export const getInviterOrderList = params =>
  requestApi(
    '/Sellerinviter/order_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page,
      client_type: 'wap'
    },
    'seller'
  )
