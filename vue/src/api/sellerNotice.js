import { requestApi } from '../util/network'

// 获取店铺通知列表
export const getNoticeList = params =>
  requestApi(
    '/seller_message/get_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'seller'
  )
