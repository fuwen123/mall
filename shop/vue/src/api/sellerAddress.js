import { requestApi } from '../util/network'
// 获取发货地址列表
export const getAddressList = () => requestApi('/Selleraddress/address_list', 'POST', {}, 'seller')
// 获取单条发货地址信息
export const getAddressInfo = addressId =>
  requestApi(
    '/Selleraddress/address_info',
    'POST',
    {
      address_id: addressId
    },
    'seller'
  )

// 编辑用户发货地址
export const editAddress = (data, addressId) =>
  requestApi(
    '/Selleraddress/address_add',
    'POST',
    {
      address_id: addressId,
      seller_name: data.seller_name,
      area_id: data.area_id,
      city_id: data.city_id,
      area_info: data.area_info,
      address: data.daddress_detail,
      telphone: data.daddress_telphone,
      is_default: data.daddress_isdefault
    },
    'seller'
  )
// 删除用户发货地址
export const delAddress = addressId =>
  requestApi(
    '/Selleraddress/address_del',
    'POST',
    {
      address_id: addressId
    },
    'seller'
  )
