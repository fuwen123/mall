import { requestApi } from '../util/network'
// 获取用户提现账户
export const getBankList = () => requestApi('/Memberbank/bank_list', 'POST', {}, 'member')
// 获取单条提现账户
export const getBankInfo = memberbankId =>
  requestApi(
    '/Memberbank/bank_info',
    'POST',
    {
      memberbank_id: memberbankId
    },
    'member'
  )
// 新增用户提现账户
export const addBank = data =>
  requestApi(
    '/Memberbank/bank_add',
    'POST',
    {
      memberbank_type: data.memberbank_type,
      memberbank_truename: data.memberbank_truename,
      memberbank_name: data.memberbank_name,
      memberbank_no: data.memberbank_no
    },
    'member'
  )
// 编辑用户提现账户
export const editBank = (data, memberbankId) =>
  requestApi(
    '/Memberbank/bank_edit',
    'POST',
    {
      memberbank_id: memberbankId,
      memberbank_type: data.memberbank_type,
      memberbank_truename: data.memberbank_truename,
      memberbank_name: data.memberbank_name,
      memberbank_no: data.memberbank_no
    },
    'member'
  )
// 删除用户提现账户
export const delBank = memberbankId =>
  requestApi(
    '/Memberbank/bank_del',
    'POST',
    {
      memberbank_id: memberbankId
    },
    'member'
  )
