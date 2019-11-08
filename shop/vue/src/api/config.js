import { requestApi } from '../util/network'

// 获取系统配置
export const getConfigList = () => requestApi('/index/getConfigList', 'POST', {})
