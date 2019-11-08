import { requestApi } from '../util/network'

// 获取虚拟订单列表
export const getOrderList = (params, order_sn, state_type) =>
  requestApi(
    '/Sellervrorder/order_list',
    'POST',
    {
      order_sn: order_sn,
      state_type: state_type,
      page: params.page,
      per_page: params.per_page
    },
    'seller'
  )
// 获取订单信息
export const getOrderInfo = orderId =>
  requestApi(
    '/Sellervrorder/order_info',
    'POST',
    {
      order_id: orderId
    },
    'seller'
  )
// 取消订单
export const cancelOrder = (orderId, reason) =>
  requestApi(
    '/Sellervrorder/order_cancel',
    'POST',
    {
      order_id: orderId,
      reason: reason
    },
    'seller'
  )
// 核销兑换码
export const exchangeCode = vr_code =>
  requestApi(
    '/Sellervrorder/exchange',
    'POST',
    {
      vr_code: vr_code
    },
    'seller'
  )
