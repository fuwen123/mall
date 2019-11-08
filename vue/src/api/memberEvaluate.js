import { requestApi } from '../util/network'

// 获取用户商品评价列表
export const getMemberevaluateList = params =>
  requestApi(
    '/Memberevaluate/get_goodsevallist',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
