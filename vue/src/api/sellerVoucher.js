import { requestApi } from '../util/network'

// 获取代金券列表
export const getVoucherList = params =>
  requestApi(
    '/Sellervoucher/templatelist',
    'POST',
    {
      page: params.page,
      per_page: params.per_page
    },
    'seller'
  )
// 获取代金券
export const getVoucherInfo = tid =>
  requestApi(
    '/Sellervoucher/templateinfo',
    'POST',
    {
      tid: tid
    },
    'seller'
  )
// 续期套餐
export const addQuota = quota_quantity =>
  requestApi(
    '/Sellervoucher/quotaadd',
    'POST',
    {
      quota_quantity: quota_quantity
    },
    'seller'
  )
// 获取公共数据
export const getCommonData = () => requestApi('/Sellervoucher/get_common_data', 'POST', {}, 'seller')
// 新增代金券
export const addVoucher = (
  txt_template_title,
  txt_template_total,
  select_template_price,
  txt_template_limit,
  txt_template_describe,
  txt_template_enddate,
  storeclass_id,
  eachlimit
) =>
  requestApi(
    '/Sellervoucher/templateadd',
    'POST',
    {
      txt_template_title: txt_template_title,
      txt_template_total: txt_template_total,
      select_template_price: select_template_price,
      txt_template_limit: txt_template_limit,
      txt_template_describe: txt_template_describe,
      txt_template_enddate: txt_template_enddate,
      storeclass_id: storeclass_id,
      eachlimit: eachlimit
    },
    'seller'
  )
// 编辑代金券
export const editVoucher = (
  tid,
  txt_template_title,
  txt_template_total,
  select_template_price,
  txt_template_limit,
  txt_template_describe,
  txt_template_enddate,
  storeclass_id,
  eachlimit,
  tstate
) =>
  requestApi(
    '/Sellervoucher/templateedit',
    'POST',
    {
      tid: tid,
      txt_template_title: txt_template_title,
      txt_template_total: txt_template_total,
      select_template_price: select_template_price,
      txt_template_limit: txt_template_limit,
      txt_template_describe: txt_template_describe,
      txt_template_enddate: txt_template_enddate,
      storeclass_id: storeclass_id,
      eachlimit: eachlimit,
      tstate: tstate
    },
    'seller'
  )
