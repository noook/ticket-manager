/* eslint-disable */

import store from '@/store';
import axios from 'axios';

const api = axios.create();
api.interceptors.request.use(
  (config) => {
    const token = store.state.AUTH_TOKEN || null;
    config.headers.common['X-AUTH-TOKEN'] = token;
    return config;
  },
  (error) => Promise.reject(error));

api.interceptors.response.use(
  (response) => {
    console.log(response.headers);
    return response;
  },
  (error) => Promise.reject(error));

export default api;

export function checkConnexion() {
  return axios.get('http://ticket-manager.ml/user/check-connection', {
    headers: {
      'X-AUTH-TOKEN': store.state.AUTH_TOKEN || null,
    },
  })
    .then(response => store.dispatch('setUser', response.data.username))
    .catch(() => store.dispatch('logout'));
};
