import { requestApi } from '../util/network'
// 获取砍价详情
export const getBargainInfo = bargain_id =>
  requestApi('/Bargain/get_info', 'POST', {
    bargain_id: bargain_id
  })
// 获取砍价详情
export const getBargainOrderInfo = (bargainorder_id, key) =>
  requestApi('/Bargain/get_order_info', 'POST', {
    bargainorder_id: bargainorder_id,
    key: key
  })
// 获取砍价列表
export const getBargainList = params =>
  requestApi('/Bargain/get_list', 'POST', {
    page: params.page,
    per_page: params.per_page
  })
// 获取砍价记录
export const getBargainLogList = (bargainorder_id, params) =>
  requestApi('/Bargain/get_log_list', 'POST', {
    bargainorder_id: bargainorder_id,
    page: params.page,
    per_page: params.per_page
  })
