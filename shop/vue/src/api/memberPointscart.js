import { requestApi } from '../util/network'
// 积分商品列表
export const cartGet = () => requestApi('/Pointcart/cart_list', 'POST', {}, 'member')
// 删除购物车
export const cartDelete = pcartId =>
  requestApi(
    '/Pointcart/cart_del',
    'POST',
    {
      pcart_id: pcartId
    },
    'member'
  )

// 购物车更新
export const cartUpdate = (pcartId, quantity) =>
  requestApi(
    '/Pointcart/cart_edit_quantity',
    'POST',
    {
      pcart_id: pcartId,
      quantity: quantity
    },
    'member'
  )
// 积分商品加入购物车
export const cartAdd = (pgid, quantity) =>
  requestApi(
    '/Pointcart/add',
    'POST',
    {
      pgid: pgid,
      quantity: quantity
    },
    'member'
  )
// 购物车数量
export const cartQuantity = () => requestApi('/Pointcart/cart_count', 'POST', {}, 'member')
// 兑换积分商品步骤1
export const buyStep1 = (cartId, ifcart) =>
  requestApi(
    '/Pointcart/step1',
    'POST',
    {
      cart_id: cartId,
      ifcart: ifcart
    },
    'member'
  )
// 兑换积分商品步骤2
export const buyStep2 = (cartId, ifcart, addressId, message) =>
  requestApi(
    '/Pointcart/step2',
    'POST',
    {
      cart_id: cartId,
      ifcart: ifcart,
      address_options: addressId,
      pcart_message: message
    },
    'member'
  )
