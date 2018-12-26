<template>
  <div id="app">
    <Navbar />
    <div class="router-view" :class="{'full-screen': fullscreen}">
      <transition>
        <router-view />
      </transition>
    </div>
  </div>
</template>

<script>
import Navbar from '@/components/Navbar.vue';

export default {
  components: {
    Navbar,
  },
  computed: {
    fullscreen() {
      return ['register', 'login'].includes(this.$route.name);
    },
  },
};
</script>


<style lang="scss">
html {
  box-sizing: border-box;
  font-size: 16px;
  scroll-behavior: smooth;
}
body, h1, h2, h3, h4, h5, h6, p, ol, ul {
  margin: 0;
  padding: 0;
  font-weight: normal;
  font-size: 100%;
  font: inherit;
  vertical-align: baseline;
}
a {
  color: inherit;
  text-decoration: inherit;
}
ol, ul {
  list-style: none;
}
img {
  max-width: 100%;
  height: auto;
}

#app {
  font-family: 'Futura PT', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: $flatBlack;
  /* margin-top: 60px; */

  .fade-enter-active,
  .fade-leave-active {
    transition-duration: 0.1s;
    transition-property: opacity;
    transition-timing-function: ease;
  }
  .fade-enter,
  .fade-leave-active {
    opacity: 0
  }

  .router-view {
    margin-top: 60px;

    &.full-screen {
      margin-top: inherit;
    }
  }
}
</style>
