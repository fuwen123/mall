import axios from 'axios'
import router from '../router'
import store from '../store'
import { urlencode } from './common'
export function requestApi (reqUrl, type = 'POST', data = {}, auth = '', multipart = false) {
  if (!reqUrl) {
    return
  }
  let headers = {}

  if (auth === 'distributor') {
    if (typeof store.state.distributor === 'undefined' || !store.state.distributor.token) {
      router.push({ name: 'DistributorLogin' })
    }
    headers['X-DS-KEY'] = store.state.distributor.token
  }
  if (auth === 'member') {
    // if (typeof (store.state.member) === 'undefined' || !store.state.member.token) {
    //   router.push({ name: 'HomeLogin' })
    // }
    headers['X-DS-KEY'] = store.state.member.token
  }
  if (auth === 'seller') {
    if (typeof store.state.seller === 'undefined' || !store.state.seller.token) {
      router.push({ name: 'HomeSellerlogin' })
    }
    headers['X-DS-KEY'] = store.state.seller.token
  }
  if (multipart) {
    headers['Content-Type'] = 'multipart/form-data'
  }
  type = type.toUpperCase()
  reqUrl = process.env.VUE_APP_API_HOST + reqUrl

  let axiosData
  if (type === 'POST') {
    axiosData = {
      headers: headers,
      method: type,
      url: reqUrl,
      data: data
    }
  } else {
    if (JSON.stringify(data) !== '{}') {
      reqUrl += '?' + urlencode(data)
    }
    axiosData = {
      headers: headers,
      method: type,
      url: reqUrl
    }
  }
  return new Promise((resolve, reject) => {
    axios(axiosData).then(
      res => {
        if (res.data.code === 10000) {
          resolve(res.data)
        } else if (res.data.code === 11001) {
          router.push({ name: 'HomeMemberLogin', query: { clear: 1 } }) // token过期，需要删除
        } else if (res.data.code === 13001) {
          router.push({ name: 'HomeSellerLogin', query: { clear: 1 } }) // token过期，需要删除
        } else {
          reject(res.data)
        }
      },
      error => {
        reject(error)
      }
    )
  })
}
export function requestRaw (reqUrl, type = 'POST', data = {}) {
  type = type.toUpperCase()
  let axiosData
  if (type === 'POST') {
    axiosData = {
      method: type,
      url: reqUrl,
      data: data
    }
  } else {
    if (JSON.stringify(data) !== '{}') {
      reqUrl += '?' + urlencode(data)
    }
    axiosData = {
      method: type,
      url: reqUrl
    }
  }
  return new Promise((resolve, reject) => {
    axios(axiosData).then(
      res => {
        resolve(res.data)
      },
      error => {
        reject(error)
      }
    )
  })
}
