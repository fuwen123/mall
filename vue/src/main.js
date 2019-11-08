import Vue from 'vue'
import App from './App.vue'
import 'es6-promise/auto'
// import Mint from 'mint-ui'

import { Popup, Header, Button, Picker, Spinner, Tabbar, TabItem, Swipe, SwipeItem, Radio, Field, Cell } from 'mint-ui'
import moment from 'moment'

import Vant from 'vant'
import 'vant/lib/index.css'

import router from './router'
import store from './store'
import utils from './util/util'

import 'mint-ui/lib/style.css'
import './assets/style/iconfont/iconfont.css'
import './assets/style/common.scss'

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
// Vue.use(Mint)
Vue.use(Vant)

Vue.prototype.utils = utils

moment.locale('zh-cn')
Vue.prototype.$moment = moment
Vue.config.productionTip = false

console.log(process.env)

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
