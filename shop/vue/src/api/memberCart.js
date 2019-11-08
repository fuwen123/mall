import { requestApi } from '../util/network'
// 加入购物车
export const addCart = (goods_id, quantity) =>
  requestApi(
    '/Membercart/cart_add',
    'POST',
    {
      goods_id: goods_id,
      quantity: quantity,
      client_type: 'wap'
    },
    'member'
  )
// 编辑购物车
export const editCart = (cart_id, quantity) =>
  requestApi(
    '/Membercart/cart_edit_quantity',
    'POST',
    {
      cart_id: cart_id,
      quantity: quantity,
      client_type: 'wap'
    },
    'member'
  )
// 删除购物车
export const delCart = cart_id =>
  requestApi(
    '/Membercart/cart_del',
    'POST',
    {
      cart_id: cart_id,
      client_type: 'wap'
    },
    'member'
  )
