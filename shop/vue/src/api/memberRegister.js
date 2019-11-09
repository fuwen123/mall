import { requestApi } from '../util/network'

// 用户注册
export const register = (username, password, passwordConfirm, email, inviterId) =>
  requestApi('/Login/register', 'POST', {
    username: username,
    password: password,
    password_confirm: passwordConfirm,
    email: email,
    inviter_id: inviterId,
    client: 'wap'
  })
// // 用户手机注册
// export const registerByMobile = (phone, password, passwordConfirm, captcha, inviterId) =>
//   requestApi('/Connect/sms_register', 'POST', {
//     phone: phone,
//     password: password,
//     password_confirm: passwordConfirm,
//     captcha: captcha,
//     inviter_id: inviterId,
//     client: 'wap'
//   })
