import { requestApi } from '../util/network'

//
export const getAreaTree = () => requestApi('/Area/area_app', 'POST', {})
