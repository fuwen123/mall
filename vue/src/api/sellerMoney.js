import { requestApi } from '../util/network'

// 获取店铺资金记录
export const getSellerMoneyLogList = params =>
  requestApi(
    '/Sellermoney/index',
    'POST',
    {
      page: params.page,
      per_page: params.per_page,
      client_type: 'wap'
    },
    'seller'
  )

// 获取店铺提现记录
export const getSellerMoneyWithdrawList = params =>
  requestApi(
    '/Sellermoney/withdraw_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page,
      client_type: 'wap'
    },
    'seller'
  )

// 店铺申请提现
export const addSellerMoneyWithdraw = amount =>
  requestApi(
    '/Sellermoney/withdraw_add',
    'POST',
    {
      pdc_amount: amount,
      client_type: 'wap'
    },
    'seller'
  )
