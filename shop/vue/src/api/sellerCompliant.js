import { requestApi } from '../util/network'
// 投诉列表
export const getComplaintList = params =>
  requestApi(
    '/sellercomplain/index',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'seller'
  )
export const getComplaintInfo = complain_id =>
  requestApi(
    '/sellercomplain/complain_show',
    'POST',
    {
      complain_id: complain_id
    },
    'seller'
  )
// 新增投诉
export const addComplaint = (input_complain_id, content, pic) =>
  requestApi(
    '/sellercomplain/appeal_save',
    'POST',
    {
      input_complain_id: input_complain_id,
      input_appeal_message: content,
      pic_name: pic
    },
    'seller'
  )
// 取消投诉
export const cancelComplaint = order_id =>
  requestApi(
    '/sellercomplain/complain_cancel',
    'POST',
    {
      complain_id: order_id
    },
    'seller'
  )
// 更新凭证
export const uploadComplaintPic = file => requestApi('/sellercomplain/upload_pic', 'POST', file, 'seller', true)
// 获取公共信息
export const getCommonData = (order_id, goods_id) =>
  requestApi(
    '/sellercomplain/get_common_data',
    'POST',
    {
      order_id: order_id,
      goods_id: goods_id
    },
    'seller'
  )
export const addComplaintTalk = (complain_id, complain_talk) =>
  requestApi(
    '/sellercomplain/publish_complain_talk',
    'POST',
    {
      complain_id: complain_id,
      complain_talk: complain_talk
    },
    'seller'
  )
export const getComplaintTalk = complain_id =>
  requestApi(
    '/sellercomplain/get_complain_talk',
    'POST',
    {
      complain_id: complain_id
    },
    'seller'
  )
export const handleComplain = complain_id =>
  requestApi(
    '/sellercomplain/apply_handle',
    'POST',
    {
      input_complain_id: complain_id
    },
    'seller'
  )
