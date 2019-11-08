import { requestApi } from '../util/network'
// 获取用户地址列表
export const getAddressList = () => requestApi('/Memberaddress/address_list', 'POST', {}, 'member')
// 获取单条用户地址信息
export const getAddressInfo = addressId =>
  requestApi(
    '/Memberaddress/address_info',
    'POST',
    {
      address_id: addressId
    },
    'member'
  )
// 新增用户收货地址
export const addAddress = data =>
  requestApi(
    '/Memberaddress/address_add',
    'POST',
    {
      address_realname: data.address_realname,
      area_id: data.area_id,
      city_id: data.city_id,
      area_info: data.area_info,
      address_detail: data.address_detail,
      address_longitude: data.address_longitude,
      address_latitude: data.address_latitude,
      address_tel_phone: data.address_tel_phone,
      address_mob_phone: data.address_mob_phone,
      address_is_default: data.address_is_default
    },
    'member'
  )
// 编辑用户收货地址
export const editAddress = (data, addressId) =>
  requestApi(
    '/Memberaddress/address_edit',
    'POST',
    {
      address_id: addressId,
      address_realname: data.address_realname,
      area_id: data.area_id,
      city_id: data.city_id,
      area_info: data.area_info,
      address_detail: data.address_detail,
      address_longitude: data.address_longitude,
      address_latitude: data.address_latitude,
      address_tel_phone: data.address_tel_phone,
      address_mob_phone: data.address_mob_phone,
      address_is_default: data.address_is_default
    },
    'member'
  )
// 删除用户收货地址
export const delAddress = addressId =>
  requestApi(
    '/Memberaddress/address_del',
    'POST',
    {
      address_id: addressId
    },
    'member'
  )
