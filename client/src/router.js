import Vue from 'vue';
import Router from 'vue-router';
import Home from './views/Home.vue';
import Authentication from './views/Authentication.vue';
import { checkConnexion } from './utils/mixins/api';

Vue.use(Router);

const router = new Router({
  mode: 'history',
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

router.beforeEach((to, from, next) => {
  if (!['register', 'login'].includes(to.name)) {
    checkConnexion()
      .then(() => next())
      .catch(() => next({ name: 'login' }));
  }
  next();
});

export default router;
