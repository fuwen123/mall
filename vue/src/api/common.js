import { requestApi } from '../util/network'
// 获取手机验证码
export const getSmsCaptcha = (type, phone) =>
  requestApi('/Connect/get_sms_captcha', 'GET', {
    type: type,
    phone: phone
  })
// 验证码检测
export const checkPictureCaptcha = captcha =>
  requestApi('/Seccode/check', 'POST', {
    captcha: captcha
  })
export const getWechatShare = url =>
  requestApi('/index/getWechatShare', 'POST', {
    url: url
  })
