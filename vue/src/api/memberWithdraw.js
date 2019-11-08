import { requestApi } from '../util/network'

export const getWithdrawList = params =>
  requestApi(
    '/Memberfund/pdcashlist',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
export const addWithdraw = (pdcAmount, memberbankId, password) =>
  requestApi(
    '/Recharge/pd_cash_add',
    'POST',
    {
      pdc_amount: pdcAmount,
      memberbank_id: memberbankId,
      password: password
    },
    'member'
  )
