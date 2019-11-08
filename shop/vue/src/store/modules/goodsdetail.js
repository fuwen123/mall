import { getGoodsInfo } from '../../api/homegoodsdetail'
const initState = {
  storeInfo: {},
  detailInfo: {}, // 商品详情
  mansongInfo: {}, // 满减
  voucher: {}, // 代金券
  commendList: [], // 推荐商品
  specList: {}, // 规格列表
  consultType: {}, // 商品咨询
  mbBody: [],
  isFavorate: false, // 是否收藏
  isShowcartInfo: false, // 是否显示购物车
  number: 0, // 选择商品的数量
  // ispromotion: false,  //点击促销， 根据该值来判断弹出框距离底部的距离
  type: '确定', // 点击购买和加入购物车， 购买浮层的按钮显示
  chooseinfo: {
    specification: [],
    ids: []
  }, // 选择该商品的ids和属性名称
  properties: [], // 多属性商品的属性名称值
  index: 0, // 当前点击的type值
  currentProductId: '', // 当前商品的id
  isPreviewPicture: false, // 当前是否是预览大图
  swipeId: 0, // 当前滑动的swiperid值
  promoPopstatus: false
}
// initial state
const state = {
  ...initState,
  initState () {
    return initState
  }
}

// mutations
const mutations = {
  saveStoreInfo (state, value) {
    state.storeInfo = value
  },
  // 保存商品详情， 各个组件数据共享
  saveDetailInfo (state, value) {
    state.detailInfo = value
  },
  saveConsultType (state, value) {
    state.consultType = value
  },
  saveMansongInfo (state, value) {
    state.mansongInfo = value
  },
  saveVoucher (state, value) {
    state.voucher = value
  },
  // 商品详情
  saveMbBody (state, value) {
    state.mbBody = value
  },
  saveIsFavorate (state, value) {
    state.isFavorate = value
  },
  // 商品评论
  saveCommendList (state, value) {
    state.commendList = value
  },
  // 商品规格
  saveSpecList (state, value) {
    state.specList = value
  },
  // 根据点击时是否显示购物车浮层
  saveCartState (state, value) {
    state.isShowcartInfo = value
  },

  // 保存选择的商品的数量
  saveNumber (state, number) {
    state.number = number
  },

  // 加入购物车，还是确定的文案设置
  changeType (state, value) {
    state.type = value
  },

  // 保存当钱切换的tab值
  changeIndex (state, value) {
    state.index = value
  },

  // 设置当前商品的id值
  setCurrentProductId (state, value) {
    state.currentProductId = value
  },

  // 设置当前商品的属性值
  saveChooseInfo (state, info) {
    state.chooseinfo = info
  },

  // 点击促销信息， 弹出框的位置
  changePromotion (state, data) {
    state.ispromotion = data
  },

  saveProperties (state, value) {
    state.properties = value
  },

  // 改变当前是否是预览大图的值
  setisPreviewPicture (state, value) {
    state.isPreviewPicture = value
  },

  setSwiperId (state, value) {
    state.swipeId = value
  },

  changePopstatus (state, value) {
    state.promoPopstatus = value
  }
}
// actions
const actions = {
  // 获取商品详情
  getGoodsDetail ({ commit, state }, params) {
    return new Promise((resolve, reject) => {
      commit('setCurrentProductId', params.goods_id)

      getGoodsInfo(params.goods_id, params.token, params.extra).then(res => {
        if (res.result.goods_image) {
          var goods_image_list = res.result.goods_image.split(',')
          res.result.goods_info.photos = goods_image_list
        }
        commit('saveDetailInfo', res.result.goods_info)
        commit('saveStoreInfo', res.result.store_info)
        commit('saveConsultType', res.result.consult_type)
        commit('saveVoucher', res.result.voucher)
        commit('saveMansongInfo', res.result.mansong_info)
        commit('saveCommendList', res.result.goods_commend_list)
        commit('saveSpecList', res.result.spec_list)
        commit('saveIsFavorate', res.result.is_favorate)
        commit('saveMbBody', res.result.mb_body)
        resolve(res)
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
