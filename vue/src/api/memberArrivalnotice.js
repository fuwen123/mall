import { requestApi } from '../util/network'

// 获取到货通知列表
export const getArrivalnoticeList = params =>
  requestApi(
    '/member_arrivalnotice/get_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
// 删除到货通知
export const delArrivalnotice = arrivalnotice_id =>
  requestApi(
    '/member_arrivalnotice/del',
    'POST',
    {
      arrivalnotice_id: arrivalnotice_id
    },
    'member'
  )
