const initState = {
  detailInfo: {}, // 商品详情
  commendList: [], // 推荐商品
  mbBody: [],
  isShowcartInfo: false, // 是否显示购物车
  number: 0, // 选择商品的数量
  type: '确定', // 点击购买和加入购物车， 购买浮层的按钮显示
  index: 0, // 当前点击的type值
  currentProductId: '', // 当前商品的id
  isPreviewPicture: false, // 当前是否是预览大图
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

  // 保存商品详情， 各个组件数据共享
  saveDetailInfo (state, value) {
    state.detailInfo = value
  },
  // 商品详情
  saveMbBody (state, value) {
    state.mbBody = value
  },

  // 商品评论
  saveCommendList (state, value) {
    state.commendList = value
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


  // 改变当前是否是预览大图的值
  setisPreviewPicture (state, value) {
    state.isPreviewPicture = value
  },

}

export default {
  state,
  mutations
}
