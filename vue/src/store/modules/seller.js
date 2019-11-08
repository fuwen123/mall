// initial state
const state = {
  isOnline: false,
  token: null,
  info: null,
  isTokenInvalid: false
}

// mutations
const mutations = {
  sellerLogin (state, payload) {
    state.info = payload.info
    state.info.invalid_time = parseInt((new Date().getTime()) / 1000) + 3600// 一小时更新
    state.isOnline = true
    state.token = payload.token
    state.isTokenInvalid = false
  },
  sellerLogout (state) {
    state.info = null
    state.isOnline = false
    state.token = null
  },
  sellerUpdate (state, payload) {
    if (state.info) {
      state.info = payload.info
      state.info.invalid_time = parseInt((new Date().getTime()) / 1000) + 3600// 一小时更新
    }
  },
  sellerEdit (state, payload) {
    if (state.info) {
      let i
      for (i in payload) {
        state.info[i] = payload[i]
      }
    }
  }
}

export default {
  state,
  mutations
}
