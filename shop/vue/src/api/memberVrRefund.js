import { requestApi } from '../util/network'

// 获取退款列表
export const getVrRefundList = params =>
  requestApi(
    '/Membervrrefund/index',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
// 获取单个退款信息
export const getVrRefundInfo = refundId =>
  requestApi(
    '/Membervrrefund/view',
    'POST',
    {
      refund_id: refundId
    },
    'member'
  )

// 新增部分退款
export const addVrRefund = (orderId, recId, buyerMessage) =>
  requestApi(
    '/Membervrrefund/add_refund',
    'POST',
    {
      rec_id: recId,
      order_id: orderId,
      buyer_message: buyerMessage
    },
    'member'
  )

// 获取公共信息
export const getCommonData = (orderId, recId) =>
  requestApi(
    '/Membervrrefund/refund_form',
    'POST',
    {
      order_id: orderId
    },
    'member'
  )
