import { getGoodsclassList } from '../../api/homesearch'

// initial state
const state = {
  items: [],
  currentItem: null
}

// mutations
const mutations = {
  saveGoodsclassItems (state, items) {
    state.items = items
  },
  clearGoodsclassItems (state) {
    state.items = null
  },
  saveCurrentGoodsclassItem (state, item) {
    state.currentItem = item
  },
  resetCurrentGoodsclassItem (state) {
    if (state.items && state.items.length) {
      state.currentItem = state.items[0]
    }
  }
}

// actions
const actions = {
  fetchGoodsclassList ({ commit, state }) {
    return new Promise((resolve, reject) => {
      getGoodsclassList().then((response) => {
        if (response.result && response.result.class_list && response.result.class_list.length) {
          commit('saveGoodsclassItems', response.result.class_list)
          if (!state.currentItem) {
            commit('saveCurrentGoodsclassItem', response.result.class_list[0])
          }
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
