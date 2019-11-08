import { requestApi } from '../util/network'

// 新增砍价
export const addBargain = bargain_id =>
  requestApi(
    '/member_bargain/add',
    'POST',
    {
      bargain_id: bargain_id
    },
    'member'
  )
// 新增砍价记录
export const addBargainLog = bargainorder_id =>
  requestApi(
    '/member_bargain/add_log',
    'POST',
    {
      bargainorder_id: bargainorder_id
    },
    'member'
  )
// 获取砍价列表
export const getBargainList = params =>
  requestApi(
    '/member_bargain/get_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
