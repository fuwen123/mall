import { requestApi } from '../util/network'
// 用户登录
export const login = (userName, password) =>
  requestApi('/Login/index', 'POST', {
    username: userName,
    password: password,
    client_type: 'wap'
  })
// 用户微信登录
export const wechatLogin = (ref, inviterId) =>
  requestApi('/Wxauto/login', 'GET', {
    ref: ref,
    inviter_id: inviterId
  })
// 绑定
export const bind = (type, from, openid, unionid, nickname, headimgurl, user, email, password, password2, inviter_id) =>
  requestApi('/Login/bind', 'POST', {
    type: type,
    from: from,
    openid: openid,
    unionid: unionid,
    nickname: nickname,
    headimgurl: headimgurl,
    user: user,
    email: email,
    password: password,
    password2: password2,
    inviter_id: inviter_id,
    client_type: 'wap'
  })
