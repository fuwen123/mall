import { requestApi } from '../util/network'

// 获取用户积分列表
export const getPointList = params =>
  requestApi(
    '/Memberpoints/pointslog',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )

// 获取签到日历
export const getPointSignin = search_day =>
  requestApi(
    '/Memberpoints/points_signin',
    'POST',
    {
      search_day: search_day
    },
    'member'
  )
// 签到
export const addPointSignin = () => requestApi('/Memberpoints/signin_add', 'POST', {}, 'member')
