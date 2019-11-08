import { requestApi } from '../util/network'

// 获取订单信息
export const buyStep1 = (goods_id, quantity, extra = {}) =>
  requestApi(
    '/membervrbuy/buy_step1',
    'POST',
    Object.assign(
      {
        goods_id: goods_id,
        quantity: quantity
      },
      extra
    ),
    'member'
  )

// 下单入库
export const buyStep2 = (goods_id, quantity, buyer_phone, buyer_msg, pd_pay, password, rcb_pay, extra = {}) =>
  requestApi(
    '/membervrbuy/buy_step2',
    'POST',
    Object.assign(
      {
        goods_id: goods_id,
        quantity: quantity,
        buyer_phone: buyer_phone,
        buyer_msg: buyer_msg,
        pd_pay: pd_pay,
        password: password,
        rcb_pay: rcb_pay
      },
      extra
    ),
    'member'
  )

// 获取订单支付信息
export const getVrOrderpayInfo = paySn =>
  requestApi(
    '/membervrbuy/pay',
    'POST',
    {
      pay_sn: paySn
    },
    'member'
  )
