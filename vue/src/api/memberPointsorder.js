import { requestApi } from '../util/network'

// 获取用户订单列表
export const getOrderList = (params, stateType, orderKey) =>
  requestApi(
    '/Memberpointorder/orderlist',
    'POST',
    {
      page: params.page,
      per_page: params.per_page,
      state_type: stateType,
      order_key: orderKey
    },
    'member'
  )
// 获取订单信息
export const getOrderInfo = orderId =>
  requestApi(
    '/Memberpointorder/order_info',
    'GET',
    {
      order_id: orderId
    },
    'member'
  )
// 取消订单
export const cancelOrder = orderId =>
  requestApi(
    '/Memberpointorder/cancel_order',
    'POST',
    {
      order_id: orderId
    },
    'member'
  )
// 订单收货
export const receiveOrder = orderId =>
  requestApi(
    '/Memberpointorder/receiving_order',
    'POST',
    {
      order_id: orderId
    },
    'member'
  )
