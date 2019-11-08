import { requestApi } from '../util/network'

// 获取用户代金券列表
export const getVoucherList = params =>
  requestApi(
    '/Membervoucher/voucher_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )

// 兑换代金券
export const receiveVoucher = vouchertemplate_id =>
  requestApi(
    '/Membervoucher/voucher_point',
    'POST',
    {
      tid: vouchertemplate_id
    },
    'member'
  )
