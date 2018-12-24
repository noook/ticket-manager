import Vue from 'vue';
import App from './App.vue';
import router from './router';
import store from './store';
import './utils/mixins/translations';
import api from './utils/mixins/api';

Vue.prototype.$api = api;
Vue.prototype.$lang = 'en';
Vue.config.productionTip = false;

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app');
