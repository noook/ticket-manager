import Vue from 'vue';
import Router from 'vue-router';
import Home from './views/Home.vue';
import Authentication from './views/Authentication.vue';
import TicketsList from './views/Ticket/List.vue';
import TicketsDetail from './views/Ticket/Detail.vue';
import TicketCreation from './views/Ticket/Creation.vue';
import TicketEdit from './views/Ticket/Edit.vue';
import MessageEdit from './views/Message/Edit.vue';

import AdminUserList from './views/Admin/Users/List.vue';

import { checkConnexion } from './utils/api';

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
    {
      path: '/tickets',
      name: 'tickets',
      component: TicketsList,
    },
    {
      path: '/tickets/new',
      name: 'ticket-creation',
      component: TicketCreation,
    },
    {
      path: '/tickets/:id',
      name: 'ticket-detail',
      component: TicketsDetail,
    },
    {
      path: '/tickets/:identifier/edit',
      name: 'ticket-edit',
      component: TicketEdit,
    },
    {
      path: '/tickets/:identifier/message/edit/:id',
      name: 'message-edit',
      component: MessageEdit,
    },
    {
      path: '/admin/users/',
      name: 'admin-users-list',
      component: AdminUserList,
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
