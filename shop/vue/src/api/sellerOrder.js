import { requestApi } from '../util/network'

// 获取订单列表
export const getOrderList = (params, order_sn, state_type) =>
  requestApi(
    '/Sellerorder/order_list',
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
    '/Sellerorder/order_info',
    'POST',
    {
      order_id: orderId
    },
    'seller'
  )

// 取消订单
export const cancelOrder = (orderId, reason) =>
  requestApi(
    '/Sellerorder/order_cancel',
    'POST',
    {
      order_id: orderId,
      reason: reason
    },
    'seller'
  )
// 订单发货
export const sendOrder = (orderId, shippingExpressId, shippingCode, daddressId) =>
  requestApi(
    '/Sellerorder/order_deliver_send',
    'POST',
    {
      order_id: orderId,
      shipping_express_id: shippingExpressId,
      shipping_code: shippingCode,
      daddress_id: daddressId
    },
    'seller'
  )
// 修改商品价格
export const editGoodsPrice = (orderId, price) =>
  requestApi(
    '/Sellerorder/order_spay_price',
    'POST',
    {
      order_id: orderId,
      order_fee: price
    },
    'seller'
  )
// 修改运费
export const editShippingFee = (orderId, price) =>
  requestApi(
    '/Sellerorder/order_ship_price',
    'POST',
    {
      order_id: orderId,
      shipping_fee: price
    },
    'seller'
  )
// 物流跟踪
export const getOrderDeliver = orderId =>
  requestApi(
    '/Sellerorder/search_deliver',
    'POST',
    {
      order_id: orderId
    },
    'seller'
  )
