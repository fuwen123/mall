import { requestApi } from '../util/network'
// 投诉列表
export const getConsultList = params =>
  requestApi(
    '/memberconsult/index',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
