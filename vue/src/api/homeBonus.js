import { requestApi } from '../util/network'
// 获取活动红包详情
export const getBonusDetail = bonusId =>
  requestApi('/Bonus/detail', 'GET', {
    bonus_id: bonusId
  })
// 领取红包
export const receiveBonus = bonusId =>
  requestApi(
    '/Memberbonus/receive',
    'GET',
    {
      bonus_id: bonusId
    },
    'member'
  )
// 领取红包
export const getReceiveList = params =>
  requestApi(
    '/Memberbonus/get_receive_list',
    'GET',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
