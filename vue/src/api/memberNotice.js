import { requestApi } from '../util/network'

// 获取用户通知列表
export const getNoticeList = params =>
  requestApi(
    '/member_message/get_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
