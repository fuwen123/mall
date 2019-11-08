import { requestApi } from '../util/network'

// 获取购物车
export const cartGet = () => requestApi('/Membercart/cart_list', 'POST', {}, 'member')
// 新增购物车
export const cartAdd = (goodsId, quantity, blId) =>
  requestApi(
    '/Membercart/cart_add',
    'POST',
    {
      bl_id: blId,
      goods_id: goodsId,
      quantity: quantity
    },
    'member'
  )
// 删除购物车
export const cartDelete = cartId =>
  requestApi(
    '/Membercart/cart_del',
    'POST',
    {
      cart_id: cartId
    },
    'member'
  )

// 购物车更新
export const cartUpdate = (cartId, quantity) =>
  requestApi(
    '/Membercart/cart_edit_quantity',
    'POST',
    {
      cart_id: cartId,
      quantity: quantity
    },
    'member'
  )

// 购物车数量
export const cartQuantity = () => requestApi('/Membercart/cart_count', 'POST', {}, 'member')
