// initial state
import { getConfigList } from '../../api/config'

const state = {
  config: null
}

// mutations
const mutations = {
  configSave (state, payload) {
    state.config = payload
  }

}

// actions
const actions = {
  fetchConfig ({ commit, state }) {
    return new Promise((resolve, reject) => {
      getConfigList().then(
        (response) => {
          if (response.result && response.result.config_list) {
            commit('configSave', response.result.config_list)
          }
          resolve(response)
        }, (error) => {
          reject(error)
        })
    })
  }

}
export default {
  state,
  mutations,
  actions
}
