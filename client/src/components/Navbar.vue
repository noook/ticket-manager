<template>
  <div class="navbar" :class="{'full-screen': fullscreen}">
    <router-link v-if="!loggedAs" to="/register">Register</router-link>
    <router-link v-if="!loggedAs" to="/login">Login</router-link>
    <p v-if="loggedAs">{{ loggedAs }}</p>
    <a @click="logout" v-if="loggedAs">{{ translations.LOGOUT }}</a>
  </div>
</template>

<script>
export default {
  methods: {
    logout() {
      this.$store.dispatch('logout');
      this.$router.push({ name: 'login' });
    },
  },
  computed: {
    loggedAs() {
      const username = this.$store.state.USER || null;
      // debugger; // eslint-disable-line;
      if (!username) {
        return false;
      }
      return this.translations.params('LOGGED_IN_AS', { username });
    },
    fullscreen() {
      return ['register', 'login'].includes(this.$route.name);
    },
  },
};
</script>

<style lang="scss" scoped>
  .navbar {
    position: fixed;
    top: 0;
    height: 60px;
    width: 100%;
    padding: 0 50px;
    box-sizing: border-box;

    > a {
      font-size: 1.2rem;
      margin: 0 10px;
      color: #a537fd;

      &.router-link-exact-active {
        text-decoration: underline;
      }

      &:hover {
        cursor: pointer;
        text-decoration: underline;
      }
    }

    @include d-flex-centered(flex-end);

    &.full-screen {
      background-color: inherit;

      > a {
        color: #fff;
      }
    }
  }
</style>
