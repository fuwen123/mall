import { requestApi } from '../util/network'

/**
 * 卖家登录
 */
export const sellerlogin = (sellerName, password) =>
  requestApi('/Sellerlogin/index', 'POST', {
    seller_name: sellerName,
    password: password,
    client_type: 'wap'
  })
