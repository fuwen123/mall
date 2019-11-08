import { requestApi } from '../util/network'
// 获取店铺信息
export const getStoreInfo = () => requestApi('/Sellerstore/store_info', 'POST', {}, 'seller')
export const editStoreInfo = (storeQq, storeWw, storePhone, storeMainbusiness, seoKeywords, seoDescription) =>
  requestApi(
    '/Sellerstore/store_edit',
    'POST',
    {
      store_qq: storeQq,
      store_ww: storeWw,
      store_phone: storePhone,
      store_mainbusiness: storeMainbusiness,
      seo_keywords: seoKeywords,
      seo_description: seoDescription
    },
    'seller'
  )
