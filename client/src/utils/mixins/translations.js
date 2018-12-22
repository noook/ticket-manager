/* eslint-disable */

import Vue from 'vue';
import Translation from '@/translations';

Vue.mixin({
  data() {
    const translations = new Translation(this.$lang);
    return {
      translations,
    };
  },
});