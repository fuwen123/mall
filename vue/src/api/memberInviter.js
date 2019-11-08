import { requestApi } from '../util/network'

// 检测是否有推广权限
export const checkInviter = () => requestApi('/memberinviter/check', 'POST', {}, 'member')

// 获取推广海报
export const getInviterPoster = () => requestApi('/memberinviter/index', 'POST', {}, 'member')

// 获取推广会员
export const getInviterUser = params =>
  requestApi(
    '/memberinviter/user',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
// 获取推广佣金
export const getInviterOrder = params =>
  requestApi(
    '/memberinviter/order',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
