import Vue from 'vue';
import vueMoment from 'vue-moment';
import App from './App.vue';
import router from './router';
import store from './store';
import './utils/mixins/translations';
import api from './utils/api';

Vue.use(vueMoment);
Vue.prototype.$api = api;
Vue.prototype.$lang = 'en';
Vue.config.productionTip = false;

new Vue({
  router,
  store,
  render: h => h(App),
}).$mount('#app');
