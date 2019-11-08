import { requestApi } from '../util/network'

// 获取退款列表
export const getRefundList = (params, refundType) =>
  requestApi(
    '/Sellerrefund/refund_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page,
      refund_type: refundType
    },
    'seller'
  )
// 获取单个退款信息
export const getRefundInfo = refundId =>
  requestApi(
    '/Sellerrefund/get_refund_info',
    'POST',
    {
      refund_id: refundId
    },
    'seller'
  )
// 编辑退款
export const editRefund = (refundId, returnType, sellerMessage, fsellerState) =>
  requestApi(
    '/Sellerrefund/edit',
    'POST',
    {
      refund_id: refundId,
      return_type: returnType,
      seller_message: sellerMessage,
      seller_state: fsellerState
    },
    'seller'
  )
// 收货
export const receiveRefund = (refundId, returnType) =>
  requestApi(
    '/Sellerrefund/receive',
    'POST',
    {
      return_id: refundId,
      return_type: returnType
    },
    'seller'
  )
// 物流跟踪
export const getRefundDeliver = refundId =>
  requestApi(
    '/Sellerrefund/search_deliver',
    'POST',
    {
      refund_id: refundId
    },
    'seller'
  )
