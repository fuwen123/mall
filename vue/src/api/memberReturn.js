import { requestApi } from '../util/network'

// 获取退货列表
export const getReturnList = params =>
  requestApi(
    '/Memberreturn/get_return_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
// 获取单个退货信息
export const getReturnInfo = returnId =>
  requestApi(
    '/Memberreturn/get_return_info',
    'POST',
    {
      return_id: returnId
    },
    'member'
  )

// 获取物流公司
export const getExpress = () => requestApi('/Memberreturn/get_express', 'POST', {}, 'member')
// 退货
export const sendReturn = (returnId, expressId, invoiceNo) =>
  requestApi(
    '/Memberreturn/ship_post',
    'POST',
    {
      return_id: returnId,
      express_id: expressId,
      invoice_no: invoiceNo
    },
    'member'
  )
// 延迟
export const delayReturn = returnId =>
  requestApi(
    '/Memberreturn/delay_post',
    'POST',
    {
      return_id: returnId
    },
    'member'
  )
