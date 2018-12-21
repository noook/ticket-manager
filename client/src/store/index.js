/* eslint-disable no-param-reassign */

import Vue from 'vue';
import Vuex from 'vuex';

Vue.use(Vuex);

const debug = process.env.NODE_ENV !== 'production';

export default new Vuex.Store({
  state: {
    AUTH_TOKEN: localStorage.getItem('AUTH_TOKEN') || null,
  },
  mutations: {
    SET_TOKEN(state, token) {
      state.AUTH_TOKEN = token;
      localStorage.setItem('AUTH_TOKEN', token);
    },
    LOGOUT(state) {
      delete state.AUTH_TOKEN;
      localStorage.removeItem('AUTH_TOKEN');
    },
  },
  actions: {
    setToken(context, token) {
      context.commit('SET_TOKEN', token);
    },
    logout(context) {
      context.commit('LOGOUT');
    },
  },
  strict: debug,
});
