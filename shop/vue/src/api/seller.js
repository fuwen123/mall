import { requestApi } from '../util/network'

// 卖家退出登录
export const logout = seller_name =>
  requestApi(
    '/Sellerlogout/index',
    'POST',
    {
      seller_name: seller_name,
      client: 'wap'
    },
    'seller'
  )
// 获取店铺基本信息
export const getSellerInfo = () =>
  requestApi(
    '/Sellerindex/index',
    'POST',
    {
      client_type: 'wap'
    },
    'seller'
  )
export const getStoreClass = () => requestApi('/store/get_store_class', 'POST', {})

// 获取店铺日志记录
export const getSellerLogList = () => requestApi('/Sellerlog/log_list', 'POST', {}, 'seller')

// 获取店铺的消费记录
export const getSellerCostList = () => requestApi('/Sellercost/cost_list', 'POST', {}, 'seller')
