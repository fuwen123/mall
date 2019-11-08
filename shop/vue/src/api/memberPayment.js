// 获取订单支付信息
import { requestRaw, requestApi } from '../util/network'

// 用户支付
export const pay = (paySn, payType, data, key) =>
  requestRaw(
    process.env.VUE_APP_SITE_URL +
      '?s=api/Memberpayment/' +
      payType +
      '/pay_sn/' +
      paySn +
      '/password/' +
      data.password +
      '/rcb_pay/' +
      data.rcb_pay +
      '/pd_pay/' +
      data.pd_pay +
      '/payment_code/' +
      data.payment_code +
      '/key/' +
      key +
      '/'
  )
// 获取支付方式列表
export const getPaymentList = () => requestApi('/Memberpayment/payment_list', 'POST', {}, 'member')
