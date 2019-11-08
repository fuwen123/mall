import { requestApi } from '../util/network'
// 获取营销活动详情
export const getMarketmanageInfo = (marketmanageId, token) =>
  requestApi('/Marketmanage/get_info', 'GET', {
    marketmanage_id: marketmanageId,
    key: token
  })
// 新增营销活动参与记录
export const addMarketmanagelog = marketmanageId =>
  requestApi(
    '/member_marketmanage/add_log',
    'POST',
    {
      marketmanage_id: marketmanageId
    },
    'member'
  )
// 获取营销活动参与记录
export const getMarketmanagelog = params =>
  requestApi(
    '/member_marketmanage/get_log',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
