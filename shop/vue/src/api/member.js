import { requestApi } from '../util/network'
// 用户退出登录
export const logout = username =>
  requestApi(
    '/Logout/index',
    'POST',
    {
      username: username,
      client: 'wap'
    },
    'member'
  )
// 获取用户首页信息
export const getMemberIndex = () => requestApi('/Member/index', 'POST', {}, 'member')
// 获取用户基本信息
export const getMemberInfo = () => requestApi('/Member/information', 'POST', {}, 'member')
// 更新用户基本信息
export const updateMemberInfo = memberInfo =>
  requestApi(
    '/Member/edit_information',
    'POST',
    {
      member_nickname: memberInfo.member_nickname,
      member_qq: memberInfo.member_qq,
      member_ww: memberInfo.member_ww,
      member_birthday: memberInfo.member_birthday
    },
    'member'
  )
// 更新用户头像
export const uploadMemberAvatar = file => requestApi('/Member/edit_memberavatar', 'POST', file, 'member', true)
export const uploadAuth = file => requestApi('/Member/edit_auth', 'POST', file, 'member', true)
export const dropAuth = file_name =>
  requestApi(
    '/Member/drop_auth',
    'POST',
    {
      file_name: file_name
    },
    'member'
  )
export const updateMemberAuth = (memberTruename, memberIdcard, ifConfirm) =>
  requestApi(
    '/Member/auth',
    'POST',
    {
      member_truename: memberTruename,
      member_idcard: memberIdcard,
      if_confirm: ifConfirm
    },
    'member'
  )
