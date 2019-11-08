import { requestApi } from '../util/network'

// 获取商品
export const getGoodsInfo = (id, token, extra = {}) =>
  requestApi(
    '/goods/goods_detail',
    'GET',
    Object.assign(
      {
        goods_id: id,
        key: token
      },
      extra
    )
  )
// 商品评论
export const getReviewList = (id, type, page) =>
  requestApi('/goods/goods_evaluate', 'GET', {
    goods_id: id,
    type: type,
    page: page
  })
// 组合搭配
export const productAccessoryList = id =>
  requestApi('/goods/get_bundling', 'GET', {
    goods_id: id
  })
// 收藏商品
export const productLike = id =>
  requestApi(
    '/Memberfavorites/favorites_add',
    'POST',
    {
      goods_id: id
    },
    'member'
  )
// 取消收藏
export const productUnlike = id =>
  requestApi(
    '/Memberfavorites/favorites_del',
    'POST',
    {
      fav_id: id
    },
    'member'
  )
// 获取商品
export const getGoodsConsult = (params, id) =>
  requestApi('/goods/consulting_list', 'GET', {
    goods_id: id,
    page: params.page,
    per_page: params.per_page
  })
export const addGoodsConsult = (id, consult_type_id, content, token) =>
  requestApi('/goods/save_consult', 'POST', {
    goods_content: content,
    consult_type_id: consult_type_id,
    goods_id: id,
    key: token
  })
