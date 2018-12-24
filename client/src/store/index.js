/* eslint-disable no-param-reassign */

import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
  state: {
    AUTH_TOKEN: localStorage.getItem('AUTH_TOKEN') || null,
    TOKEN_EXPIRACY: localStorage.getItem('TOKEN_EXPIRACY') || null,
  },
  mutations: {
    SET_TOKEN(state, data) {
      state.AUTH_TOKEN = data.token;
      state.TOKEN_EXPIRACY = data.expiracy.date;
      localStorage.setItem('AUTH_TOKEN', data.token);
      localStorage.setItem('TOKEN_EXPIRACY', data.expiracy.date);
    },
    SET_USER(state, username) {
      Vue.set(state, 'USER', username);
    },
    LOGOUT(state) {
      Vue.delete(state, 'AUTH_TOKEN');
      Vue.delete(state, 'TOKEN_EXPIRACY');
      Vue.delete(state, 'USER');
      localStorage.removeItem('AUTH_TOKEN');
      localStorage.removeItem('TOKEN_EXPIRACY');
    },
  },
  actions: {
    setToken(context, data) {
      context.commit('SET_TOKEN', data);
    },
    setUser(context, data) {
      context.commit('SET_USER', data);
    },
    logout(context) {
      context.commit('LOGOUT');
    },
  },
  strict: debug,
});
