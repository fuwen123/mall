import { requestApi } from '../util/network'
// 获取物流服务列表
export const getExpressList = orderId =>
  requestApi(
    '/Sellerexpress/get_mylist',
    'POST',
    {
      order_id: orderId
    },
    'seller'
  )
// 获取默认发货信息
export const getExpressInfo = orderId =>
  requestApi(
    '/Sellerexpress/get_defaultexpress',
    'POST',
    {
      order_id: orderId
    },
    'seller'
  )
