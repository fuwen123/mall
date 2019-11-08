import { requestApi } from '../util/network'

// 获取店铺商品分类公共数据
export const getCommonData = () => requestApi('/Sellergoodsclass/get_common_data', 'POST', {}, 'seller')
// 获取店铺商品分类列表
export const getGoodsClassList = () => requestApi('/Sellergoodsclass/index', 'POST', {}, 'seller')

// 获取店铺商品分类信息
export const getGoodsClassInfo = top_class_id =>
  requestApi(
    '/Sellergoodsclass/goods_class_edit',
    'POST',
    {
      top_class_id: top_class_id
    },
    'seller'
  )

// 删除店铺商品分类
export const delGoodsClass = class_id =>
  requestApi(
    '/Sellergoodsclass/drop_goods_class',
    'POST',
    {
      class_id: class_id
    },
    'seller'
  )

// 编辑店铺商品分类
export const editGoodsClass = data =>
  requestApi(
    '/Sellergoodsclass/goods_class_save',
    'POST',
    {
      storegc_id: data.storegc_id,
      storegc_name: data.storegc_name,
      storegc_parent_id: data.storegc_parent_id,
      storegc_state: data.storegc_state,
      storegc_sort: data.storegc_sort
    },
    'seller'
  )
