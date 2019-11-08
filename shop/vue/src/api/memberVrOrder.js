import { requestApi } from '../util/network'

// 获取用户订单列表
export const getOrderList = (parmas, stateType, orderKey) =>
  requestApi(
    '/Membervrorder/order_list',
    'POST',
    {
      page: parmas.page,

      per_page: parmas.per_page,
      state_type: stateType,
      order_key: orderKey
    },
    'member'
  )
// 获取订单信息
export const getOrderInfo = orderId =>
  requestApi(
    '/Membervrorder/order_info',
    'POST',
    {
      order_id: orderId
    },
    'member'
  )
// 订单评价
export const saveOrderEvaluate = (orderId, result) =>
  requestApi(
    '/Memberevaluate/save_vr',
    'POST',
    Object.assign(
      {
        order_id: orderId
      },
      result
    ),
    'member'
  )
// 获取评价信息
export const getOrderEvaluateInfo = orderId =>
  requestApi(
    '/Memberevaluate/vr',
    'POST',
    {
      order_id: orderId
    },
    'member'
  )
// 取消订单
export const cancelOrder = orderId =>
  requestApi(
    '/Membervrorder/order_cancel',
    'POST',
    {
      order_id: orderId
    },
    'member'
  )
