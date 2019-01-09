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
  (error) => {
    Promise.reject(error);
  });

api.interceptors.response.use(
  (response) => {
    // Do something with response data
    return response;
  },
  (error) => {
    // Do something with response error
    return Promise.reject(error);
  });

export default api;

export function checkConnexion() {
  return new Promise((resolve, reject) => api.get('http://ticket-manager.ml/user/check-connection', {
    headers: {
      'X-AUTH-TOKEN': store.state.AUTH_TOKEN || null,
    },
  })
    .then(response => {
      store.dispatch('setUser', response.data);
      if (response.headers['x-refreshed-token']) {
        store.dispatch('setToken', {
          token: response.headers['x-refreshed-token'],
          expiracy: response.headers['x-token-expiracy'],
        });
      }
      resolve();
    })
    .catch((err) => {
      store.dispatch('logout');
      reject();
    }));
};
