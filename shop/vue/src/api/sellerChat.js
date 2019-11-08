import { requestApi } from '../util/network'

// 获取聊天信息
export const getChatInfo = (userId, goodsId) =>
  requestApi(
    '/Sellerchat/get_node_info',
    'POST',
    {
      u_id: userId,
      chat_goods_id: goodsId
    },
    'seller'
  )
// 发送聊天
export const addChat = data => requestApi('/Sellerchat/send_msg', 'POST', data, 'seller')
// 聊天历史
export const getChatHistory = (params, t_id) =>
  requestApi(
    '/Sellerchat/get_chat_log',
    'POST',
    {
      page: params.page,
      per_page: params.per_page,
      t_id: t_id
    },
    'seller'
  )
// 最近消息
export const getChatList = () =>
  requestApi(
    '/Sellerchat/get_user_list',
    'POST',
    {
      recent: 1
    },
    'seller'
  )
