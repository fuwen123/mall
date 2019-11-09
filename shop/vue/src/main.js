import Vue from 'vue'
import App from './App.vue'
import 'es6-promise/auto'
import VueLazyload from 'vue-lazyload'

import Vant from 'vant'
import 'vant/lib/index.css'

// import VConsole from 'vconsole'

import router from './router'
import store from './store'
import utils from './util/util'

import { Popup, Header, Button, Picker, Spinner, Tabbar, TabItem, Swipe, SwipeItem, Radio, Field, Cell } from 'mint-ui'
import 'mint-ui/lib/style.css'

import './assets/style/iconfont/iconfont.css'
import './assets/style/common.scss'

// JS库
import moment from 'moment'
import _ from 'underscore'

// // mini-ui 组件
Vue.component(Popup.name, Popup)
Vue.component(Header.name, Header)
Vue.component(Button.name, Button)
Vue.component(Picker.name, Picker)
Vue.component(Spinner.name, Spinner)
Vue.component(Tabbar.name, Tabbar)
Vue.component(TabItem.name, TabItem)
Vue.component(Swipe.name, Swipe)
Vue.component(SwipeItem.name, SwipeItem)
Vue.component(Radio.name, Radio)
Vue.component(Field.name, Field)
Vue.component(Cell.name, Cell)

Vue.use(Vant).use(VueLazyload)

// 挂载JS库
Vue.prototype.utils = utils
Vue.prototype.$moment = moment
Vue.prototype._ = _
Vue.config.productionTip = false
// import vconsole
// new VConsole()

router.afterEach((to, from) => {
  document.title = to.meta.head.title || '蜂鸟智慧商圈' // title 更改
  // document.body.className = to.meta.body || '' // 动态改变 body class
})

new Vue({
  store,
  router,
  render: h => h(App)
}).$mount('#app')
// new Vue({
//     el: '#app',
//     store,
//     router,
//     components: { App },
//     template: '<App/>'
// })
