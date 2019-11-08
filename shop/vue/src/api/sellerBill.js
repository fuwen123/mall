import { requestApi } from '../util/network'

// 获取结算列表
export const getBillList = (params, ob_no, bill_state) =>
  requestApi(
    '/Sellerbill/bill_list',
    'POST',
    {
      ob_no: ob_no,
      bill_state: bill_state,
      page: params.page,
      per_page: params.per_page,
      client_type: 'wap'
    },
    'seller'
  )
// 确认结算单
export const confirmBill = (obNo, content) =>
  requestApi(
    '/Sellerbill/confirm_bill',
    'POST',
    {
      ob_no: obNo,
      ob_seller_content: content
    },
    'seller'
  )
