import Vue from 'vue';
import Router from 'vue-router';
import Home from './views/Home.vue';
import Authentication from './views/Authentication.vue';

Vue.use(Router);

export default new Router({
  mode: 'history',
  // beforeEach(to, from, next) {
  //   // ...
  // },
  routes: [
    {
      path: '/',
      name: 'home',
      component: Home,
    },
    {
      path: '/register',
      name: 'register',
      component: Authentication,
    },
    {
      path: '/login',
      name: 'login',
      component: Authentication,
    },
  ],
});
