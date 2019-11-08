import { requestApi } from '../util/network'

// 获取聊天信息
export const getChatInfo = (userId, goodsId) =>
  requestApi(
    '/Memberchat/get_node_info',
    'POST',
    {
      u_id: userId,
      chat_goods_id: goodsId
    },
    'member'
  )
// 发送聊天
export const addChat = data => requestApi('/Memberchat/send_msg', 'POST', data, 'member')
// 聊天历史
export const getChatHistory = (params, t_id) =>
  requestApi(
    '/Memberchat/get_chat_log',
    'POST',
    {
      page: params.page,
      per_page: params.per_page,
      t_id: t_id
    },
    'member'
  )
// 最近消息
export const getChatList = () =>
  requestApi(
    '/Memberchat/get_user_list',
    'POST',
    {
      recent: 1
    },
    'member'
  )
// 新消息数
export const getChatCount = () => requestApi('/Memberchat/get_msg_count', 'POST', {}, 'member')
