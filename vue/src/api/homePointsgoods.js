import { requestApi } from '../util/network'

// 获取积分兑换商品列表
export const getPointsgoodsList = params =>
  requestApi('/Pointprod/index', 'POST', {
    page: params.page,
    per_page: params.per_page,
    client_type: 'wap'
  })
// 获取积分兑换商品详情
export const getPointsgoodsInfo = id =>
  requestApi('/Pointprod/pinfo', 'POST', {
    id: id
  })
// 获取积分兑换商品订单列表
export const getPointsorderList = (params, id) =>
  requestApi('/Pointprod/get_order_list', 'POST', {
    page: params.page,
    per_page: params.per_page,
    pgoods_id: id
  })
