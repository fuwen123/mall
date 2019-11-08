import { requestApi } from '../util/network'

// 获取用户预存款记录
export const getPredepositList = params =>
  requestApi(
    '/Memberfund/predepositlog',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
// 获取用户充值卡记录
export const getRechargeCardList = params =>
  requestApi(
    '/Memberfund/rcblog',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
// 增加充值卡
export const addRechargeCard = rc_sn =>
  requestApi(
    '/Memberfund/rechargecard_add',
    'POST',
    {
      rc_sn: rc_sn
    },
    'member'
  )
