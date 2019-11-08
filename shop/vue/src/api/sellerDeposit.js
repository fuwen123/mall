import { requestApi } from '../util/network'

// 获取店铺保证金记录
export const getSellerDepositList = params =>
  requestApi(
    '/Sellerdeposit/index',
    'POST',
    {
      page: params.page,
      per_page: params.per_page,
      client_type: 'wap'
    },
    'seller'
  )

// 获取店铺保证金提现记录
export const getSellerDepositWithdrawList = params =>
  requestApi(
    '/Sellerdeposit/withdraw_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page,
      client_type: 'wap'
    },
    'seller'
  )

// 补缴店铺保证金
export const addSellerDeposit = amount =>
  requestApi(
    '/Sellerdeposit/recharge_add',
    'POST',
    {
      pdc_amount: amount,
      client_type: 'wap'
    },
    'seller'
  )
// 取出店铺保证金
export const reduceSellerDeposit = amount =>
  requestApi(
    '/Sellerdeposit/withdraw_add',
    'POST',
    {
      pdc_amount: amount,
      client_type: 'wap'
    },
    'seller'
  )
