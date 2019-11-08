import { requestApi } from '../util/network'

// 获取用户发票列表
export const getInvoiceList = () => requestApi('/Memberinvoice/invoice_list', 'POST', {}, 'member')
// 获取单个发票信息
export const getInvoiceInfo = invoiceId =>
  requestApi(
    '/Memberinvoice/invoice_info',
    'POST',
    {
      invoice_id: invoiceId
    },
    'member'
  )
// 新增发票
export const addInvoice = data =>
  requestApi(
    '/Memberinvoice/invoice_add',
    'POST',
    {
      invoice_state: data.invoice_state,
      invoice_title: data.invoice_title,
      invoice_code: data.invoice_code,
      invoice_content: data.invoice_content,
      invoice_company: data.invoice_company,
      invoice_company_code: data.invoice_company_code,
      invoice_reg_addr: data.invoice_reg_addr,
      invoice_reg_phone: data.invoice_reg_phone,
      invoice_reg_bname: data.invoice_reg_bname,
      invoice_reg_baccount: data.invoice_reg_baccount
    },
    'member'
  )
// 编辑发票
export const editInvoice = (data, invoiceId) =>
  requestApi(
    '/Memberinvoice/invoice_edit',
    'POST',
    {
      invoice_id: invoiceId,
      invoice_state: data.invoice_state,
      invoice_title: data.invoice_title,
      invoice_code: data.invoice_code,
      invoice_content: data.invoice_content,
      invoice_company: data.invoice_company,
      invoice_company_code: data.invoice_company_code,
      invoice_reg_addr: data.invoice_reg_addr,
      invoice_reg_phone: data.invoice_reg_phone,
      invoice_reg_bname: data.invoice_reg_bname,
      invoice_reg_baccount: data.invoice_reg_baccount
    },
    'member'
  )
// 删除发票
export const delInvoice = invoiceId =>
  requestApi(
    '/Memberinvoice/invoice_del',
    'POST',
    {
      invoice_id: invoiceId
    },
    'member'
  )
