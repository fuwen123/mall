import { homeIndexAdList, homeIndexProductList } from '../../api/homeindex'
import { getArticleList } from '../../api/homeArticle'

// initial state
const state = {
  banners: null, // 首页Banner
  navs: null, // 首页导航
  floorAds: null, // 横屏广告图
  promotionAds: null, // 促销广告图 3 张
  articles: null, // 商城公告分类下的文章 首页显示
  hotProducts: null, // 首页热门产品
  recentlyProducts: null,
  goodProducts: null
}

// mutations
const mutations = {
  saveHomeAds (state, data) {
    state.banners = data.result.banners
    state.navs = data.result.navs
    state.floorAds = data.result.floor_ads
    state.promotionAds = data.result.promotion_ads
  },
  saveHomeArticles (state, data) {
    state.articles = data.result.article_list
  },
  saveHomeProducts (state, data) {
    state.hotProducts = data.result.hot_products
    state.recentlyProducts = data.result.recently_products
    state.goodProducts = data.result.good_products
  }
}

// actions
const actions = {
  // 获取首页轮播图
  fetchHomeAd ({ commit, state }, params) {
    return new Promise((resolve, reject) => {
      homeIndexAdList().then(
        (response) => {
          commit('saveHomeAds', response)
          resolve(response)
        }, (error) => {
          reject(error)
        })
    })
  },
  // 获取首页商城公告分类下文章
  fetchHomeArticle ({ commit, state }, params) {
    return new Promise((resolve, reject) => {
      // 文章分类ID为1 下的文章列表
      getArticleList('', 1).then(
        (response) => {
          commit('saveHomeArticles', response)
          resolve(response)
        }, (error) => {
          reject(error)
        })
    })
  },
  // 获取首页产品
  fetchHomeProduct ({ commit, state }, params) {
    return new Promise((resolve, reject) => {
      homeIndexProductList(params.name).then(
        (response) => {
          commit('saveHomeProducts', response)
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
