import { requestApi } from '../util/network'

// 获取代金券列表
export const getVoucherList = data =>
  requestApi('/Voucher/voucher_list', 'GET', {
    page: data.page, // 当前第几页
    per_page: data.per_page // 每页多少
  })
