import { requestApi } from '../util/network'

// 获取店铺账户组列表
export const getCommonData = () => requestApi('/Selleraccountgroup/get_common_data', 'POST', {}, 'seller')
// 获取店铺子账户列表
export const getAccountGroupList = () => requestApi('/Selleraccountgroup/group_list', 'POST', {}, 'seller')

// 获取店铺单个子账户信息
export const getAccountGroupInfo = group_id =>
  requestApi(
    '/Selleraccountgroup/group_edit',
    'POST',
    {
      group_id: group_id
    },
    'seller'
  )

// 删除店铺子账户
export const delAccountGroup = group_id =>
  requestApi(
    '/Selleraccountgroup/group_del',
    'POST',
    {
      group_id: group_id
    },
    'seller'
  )

// 编辑店铺子账户
export const editAccountGroup = (group_id, seller_group_name, menu, smt) =>
  requestApi(
    '/Selleraccountgroup/group_save',
    'POST',
    {
      group_id: group_id,
      seller_group_name: seller_group_name,
      limits: menu,
      smt_limits: smt
    },
    'seller'
  )
