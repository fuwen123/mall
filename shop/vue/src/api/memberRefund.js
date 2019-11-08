import { requestApi } from '../util/network'

// 获取退款列表
export const getRefundList = params =>
  requestApi(
    '/Memberrefund/get_refund_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
// 获取单个退款信息
export const getRefundInfo = refundId =>
  requestApi(
    '/Memberrefund/get_refund_info',
    'POST',
    {
      refund_id: refundId
    },
    'member'
  )
// 新增全部退款
export const addRefundAll = (orderId, buyerMessage, fileValue) =>
  requestApi(
    '/Memberrefund/refund_all_post',
    'POST',
    {
      order_id: orderId,
      buyer_message: buyerMessage,
      refund_pic: fileValue
    },
    'member'
  )
// 新增部分退款
export const addRefund = (
  refundType,
  orderId,
  orderGoodsId,
  refundAmount,
  goodsNum,
  reasonId,
  buyerMessage,
  fileValue
) =>
  requestApi(
    '/Memberrefund/refund_post',
    'POST',
    {
      refund_type: refundType,
      order_id: orderId,
      order_goods_id: orderGoodsId,
      refund_amount: refundAmount,
      goods_num: goodsNum,
      reason_id: reasonId,
      buyer_message: buyerMessage,
      refund_pic: fileValue
    },
    'member'
  )
// 上传退款凭证
export const uploadRefundImage = file => requestApi('/Memberrefund/upload_pic', 'POST', file, 'member', true)
// 获取公共信息
export const getCommonData = (orderId, recId) =>
  requestApi(
    '/Memberrefund/refund_form',
    'POST',
    {
      order_id: orderId,
      order_goods_id: recId
    },
    'member'
  )
