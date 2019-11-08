import { requestApi } from '../util/network'

export const getRechargeList = params =>
  requestApi(
    '/Memberfund/pdrechargelist',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
export const getRechargeInfo = paysn =>
  requestApi(
    '/Recharge/recharge_order',
    'POST',
    {
      paysn: paysn
    },
    'member'
  )
export const addRecharge = amount =>
  requestApi(
    '/Recharge/index',
    'POST',
    {
      pdr_amount: amount
    },
    'member'
  )
