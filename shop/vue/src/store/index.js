import Vue from 'vue'
import Vuex from 'vuex'

import createPersistedState from 'vuex-persistedstate'
import * as getters from './getter'

import goodsclass from './modules/goodsclass'// 商品分类
import goodsdetail from './modules/goodsdetail'// 商品详情
import pointsgoods from './modules/pointsgoods'// 积分商品
import home from './modules/home'// 首页
import homesearch from './modules/homesearch'// 搜索
import homecart from './modules/homecart'// 购物车
import member from './modules/member'// 用户信息
import seller from './modules/seller'// 卖家信息
import config from './modules/config'// 配置信息

Vue.use(Vuex)

export default new Vuex.Store({
  modules: {
    home,
    homesearch,
    homecart,
    goodsclass,
    goodsdetail,
    pointsgoods,
    member,
    seller,
    config
  },
  getters,
  plugins: [
    createPersistedState({
      key: 'dsmall',
      paths: ['member', 'seller', 'config', 'home', 'homecart', 'goodsdetail', 'pointsgoods']
    })
  ]
})
