import Vue from 'vue'
import App from './App'
import router from './router'
import ElementUI from 'element-ui'
import store from './store'
import 'element-ui/lib/theme-chalk/index.css'

Vue.use(ElementUI);
Vue.use(router);

Vue.config.productionTip = false;
window.bus = new Vue();
new Vue({
  el: '#app',
  router,
  store,
  render: h => h(App)
})