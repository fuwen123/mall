import { requestApi } from '../util/network'
// 获取店铺搜藏列表
export const getFavoriteStoreList = page => requestApi('/Memberfavoritesstore/favorites_list', 'POST', {}, 'member')
// 收藏店鋪
export const addFavoriteStore = storeId =>
  requestApi(
    '/Memberfavoritesstore/favorites_add',
    'POST',
    {
      store_id: storeId // 所属店铺
    },
    'member'
  )
// 删除店铺搜藏
export const delFavoriteStore = favId =>
  requestApi(
    '/Memberfavoritesstore/favorites_del',
    'POST',
    {
      fav_id: favId
    },
    'member'
  )
// 获取商品搜藏列表
export const getFavoriteGoodsList = page =>
  requestApi(
    '/Memberfavorites/favorites_list',
    'POST',
    {
      page: page
    },
    'member'
  )
// 删除商品搜藏
export const delFavoriteGoods = favId =>
  requestApi(
    '/Memberfavorites/favorites_del',
    'POST',
    {
      fav_id: favId
    },
    'member'
  )
