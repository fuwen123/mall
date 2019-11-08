import { requestApi } from '../util/network'
// 投诉列表
export const getComplaintList = params =>
  requestApi(
    '/membercomplain/index',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
// 新增投诉
export const addComplaint = (order_id, goods_id, subject, content, pic) =>
  requestApi(
    '/membercomplain/complain_save',
    'POST',
    {
      input_order_id: order_id,
      input_goods_id: goods_id,
      input_complain_subject: subject,
      input_complain_content: content,
      pic_name: pic
    },
    'member'
  )
// 取消投诉
export const cancelComplaint = order_id =>
  requestApi(
    '/membercomplain/complain_cancel',
    'POST',
    {
      complain_id: order_id
    },
    'member'
  )
// 更新凭证
export const uploadComplaintPic = file => requestApi('/membercomplain/upload_pic', 'POST', file, 'member', true)
// 获取公共信息
export const getCommonData = (order_id, goods_id) =>
  requestApi(
    '/membercomplain/get_common_data',
    'POST',
    {
      order_id: order_id,
      goods_id: goods_id
    },
    'member'
  )
export const getComplaintInfo = complain_id =>
  requestApi(
    '/membercomplain/complain_show',
    'POST',
    {
      complain_id: complain_id
    },
    'member'
  )
export const addComplaintTalk = (complain_id, complain_talk) =>
  requestApi(
    '/membercomplain/publish_complain_talk',
    'POST',
    {
      complain_id: complain_id,
      complain_talk: complain_talk
    },
    'member'
  )
export const getComplaintTalk = complain_id =>
  requestApi(
    '/membercomplain/get_complain_talk',
    'POST',
    {
      complain_id: complain_id
    },
    'member'
  )
export const handleComplain = complain_id =>
  requestApi(
    '/membercomplain/apply_handle',
    'POST',
    {
      input_complain_id: complain_id
    },
    'member'
  )
