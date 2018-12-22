<template>
  <div class="navbar" :class="{'full-screen': fullscreen}">
    <router-link to="/register">Register</router-link>
    <router-link to="/login">Login</router-link>
  </div>
</template>

<script>
export default {
  computed: {
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
