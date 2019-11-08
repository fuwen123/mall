import { requestApi } from '../util/network'

// 获取评价列表
export const getEvaluateList = (params, goods_name, member_name) =>
  requestApi(
    '/Sellerevaluate/evaluate_list',
    'POST',
    {
      goods_name: goods_name,
      member_name: member_name,
      page: params.page,
      per_page: params.per_page
    },
    'seller'
  )
// 解释评论
export const addExplain = (gevalId, gevalExplain) =>
  requestApi(
    '/Sellerevaluate/explain_save',
    'POST',
    {
      geval_id: gevalId,
      geval_explain: gevalExplain
    },
    'seller'
  )
