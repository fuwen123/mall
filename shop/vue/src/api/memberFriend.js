import { requestApi } from '../util/network'

// 添加好友
export const addFriend = mId =>
  requestApi(
    '/Membersnsfriend/friend_add',
    'POST',
    {
      m_id: mId
    },
    'member'
  )
export const delFriend = mId =>
  requestApi(
    '/Membersnsfriend/friend_del',
    'POST',
    {
      m_id: mId
    },
    'member'
  )
// 好友列表
export const getFriendList = params =>
  requestApi(
    '/Membersnsfriend/friend_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'member'
  )
// 好友列表
export const searchFriend = (params, mName) =>
  requestApi(
    '/Membersnsfriend/member_list',
    'POST',
    {
      page: params.page,
      per_page: params.per_page,
      m_name: mName
    },
    'member'
  )
