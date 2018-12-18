<template>
  <div id="app">
    <div id="nav">
      <router-link to="/about">About</router-link>
      <router-link to="/register">Register</router-link>
    </div>
    <router-view/>
  </div>
</template>

<script>
export default {

};
</script>


<style>
#app {
  font-family: 'Futura PT', Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
}
</style>
