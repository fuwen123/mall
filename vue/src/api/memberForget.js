import { requestApi } from '../util/network'

export const forget = (userName, captcha, password, confirmPassword) =>
  requestApi('/Connect/find_password', 'POST', {
    phone: userName,
    captcha: captcha,
    password: password,
    password_confirm: confirmPassword,
    client: 'wap'
  })
