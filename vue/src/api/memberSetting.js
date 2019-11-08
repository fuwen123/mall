import { requestApi } from '../util/network'
// 发送验证码
export const sendAuthCode = type =>
  requestApi(
    '/Memberaccount/send_auth_code',
    'POST',
    {
      type: type
    },
    'member'
  )
// 验证码检测
export const checkAuthCode = authCode =>
  requestApi(
    '/Memberaccount/check_auth_code',
    'POST',
    {
      auth_code: authCode
    },
    'member'
  )
// 更新用户手机号
export const updateUserMobile = authCode =>
  requestApi(
    '/Memberaccount/bind_mobile_step2',
    'POST',
    {
      auth_code: authCode
    },
    'member'
  )
// 更新用户密码
export const updateUserPassword = (password, password1) =>
  requestApi(
    '/Memberaccount/modify_password',
    'POST',
    {
      password: password,
      password1: password1
    },
    'member'
  )
// 更新用户支付密码
export const updateUserPaypwd = (password, password1) =>
  requestApi(
    '/Memberaccount/modify_paypwd',
    'POST',
    {
      password: password,
      password1: password1
    },
    'member'
  )
// 绑定用户手机
export const bindUserMobile = mobile =>
  requestApi(
    '/Memberaccount/bind_mobile_step1',
    'POST',
    {
      mobile: mobile
    },
    'member'
  )
// 绑定用户邮箱
export const bindUserEmail = email =>
  requestApi(
    '/Memberaccount/bind_email_step1',
    'POST',
    {
      email: email
    },
    'member'
  )
