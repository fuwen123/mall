import { requestApi } from '../util/network'
// 投诉列表
export const getInformList = params =>
  requestApi(
    '/memberinform/index',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
// 新增投诉
export const addInform = (goods_id, subject, content, pic) =>
  requestApi(
    '/memberinform/inform_save',
    'POST',
    {
      inform_goods_id: goods_id,
      inform_subject: subject,
      inform_content: content,
      pic_name: pic
    },
    'member'
  )
// 取消投诉
export const cancelInform = order_id =>
  requestApi(
    '/memberinform/inform_cancel',
    'POST',
    {
      inform_id: order_id
    },
    'member'
  )
// 更新凭证
export const uploadInformPic = file => requestApi('/memberinform/upload_pic', 'POST', file, 'member', true)
// 获取公共信息
export const getCommonData = goods_id =>
  requestApi(
    '/memberinform/inform_submit',
    'POST',
    {
      goods_id: goods_id
    },
    'member'
  )
export const getInformInfo = inform_id =>
  requestApi(
    '/memberinform/inform_info',
    'POST',
    {
      inform_id: inform_id
    },
    'member'
  )
export const getInformSubject = type_id =>
  requestApi(
    '/memberinform/get_subject_by_typeid',
    'POST',
    {
      type_id: type_id
    },
    'member'
  )
