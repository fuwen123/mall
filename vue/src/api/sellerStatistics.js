import { requestApi } from '../util/network'

/**
 * 获取卖家商品列表
 */
export const getStatisticsGeneral = () =>
  requestApi(
    '/Statisticsgeneral/index',
    'POST',
    {
      client_type: 'wap'
    },
    'seller'
  )
