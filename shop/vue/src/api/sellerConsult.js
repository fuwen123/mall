import { requestApi } from '../util/network'
// 商品咨询列表
export const getConsultList = params =>
  requestApi(
    '/sellerconsult/index',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'seller'
  )
// 删除商品咨询
export const delConsult = id =>
  requestApi(
    '/sellerconsult/drop_consult',
    'POST',
    {
      id: id
    },
    'seller'
  )
// 回复商品咨询
export const replyConsult = (consult_id, content) =>
  requestApi(
    '/sellerconsult/reply_save',
    'POST',
    {
      consult_id: consult_id,
      content: content
    },
    'seller'
  )
