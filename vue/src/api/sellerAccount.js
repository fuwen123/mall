import { requestApi } from '../util/network'

// 获取店铺账户组列表
export const getSellerAccountGroupList = () =>
  requestApi(
    '/Selleraccount/group_list',
    'POST',
    {
      client_type: 'wap'
    },
    'seller'
  )
// 获取店铺子账户列表
export const getSellerAccountList = () =>
  requestApi(
    '/Selleraccount/account_list',
    'POST',
    {
      client_type: 'wap'
    },
    'seller'
  )

// 获取店铺单个子账户信息
export const getSellerAccountInfo = sellerId =>
  requestApi(
    '/Selleraccount/account_info',
    'POST',
    {
      seller_id: sellerId,
      client_type: 'wap'
    },
    'seller'
  )

// 删除店铺子账户
export const delSellerAccount = sellerId =>
  requestApi(
    '/Selleraccount/account_del',
    'POST',
    {
      seller_id: sellerId,
      client_type: 'wap'
    },
    'seller'
  )

// 编辑店铺子账户
export const editSellerAccount = data =>
  requestApi(
    '/Selleraccount/account_edit',
    'POST',
    {
      seller_id: data.seller_id,
      group_id: data.sellergroup_id,
      client_type: 'wap'
    },
    'seller'
  )

// 新增店铺子账户
export const addSellerAccount = data =>
  requestApi(
    '/Selleraccount/account_add',
    'POST',
    {
      member_name: data.member_name,
      password: data.password,
      seller_name: data.seller_name,
      group_id: data.sellergroup_id,
      client_type: 'wap'
    },
    'seller'
  )
