import { requestApi } from '../util/network'
export const checkFCode = (goods_id, fcode) =>
  requestApi(
    '/memberbuy/check_fcode',
    'POST',
    {
      goods_id: goods_id,
      fcode: fcode
    },
    'member'
  )
// 获取订单信息
export const buyStep1 = (cart_id, ifcart, pintuan_id, pintuangroup_id, extra = {}) =>
  requestApi(
    '/memberbuy/buy_step1',
    'POST',
    Object.assign(
      {
        cart_id: cart_id,
        ifcart: ifcart,
        pintuan_id: pintuan_id,
        pintuangroup_id: pintuangroup_id
      },
      extra
    ),
    'member'
  )

// 下单入库
export const buyStep2 = (
  ifcart,
  cart_id,
  address_id,
  vat_hash,
  offpay_hash,
  offpay_hash_batch,
  invoice_id,
  voucher,
  pd_pay,
  password,
  rcb_pay,
  pay_message,
  pintuan_id,
  pintuangroup_id,
  f_code,
  pay_code,
  extra = {}
) =>
  requestApi(
    '/memberbuy/buy_step2',
    'POST',
    Object.assign(
      {
        ifcart: ifcart,
        cart_id: cart_id,
        address_id: address_id,
        vat_hash: vat_hash,
        offpay_hash: offpay_hash,
        offpay_hash_batch: offpay_hash_batch,
        pay_name: pay_code,
        invoice_id: invoice_id,
        voucher: voucher,
        pd_pay: pd_pay,
        password: password,
        rcb_pay: rcb_pay,
        pay_message: pay_message,
        pintuan_id: pintuan_id,
        pintuangroup_id: pintuangroup_id,
        fcode: f_code
      },
      extra
    ),
    'member'
  )

// 获取订单支付信息
export const getOrderpayInfo = paySn =>
  requestApi(
    '/memberbuy/pay',
    'POST',
    {
      pay_sn: paySn
    },
    'member'
  )
