<template>
  <div class="login">
    <form class="container w-25" @submit.prevent="login">
      <div class="form-group">
        <label for="username">Username</label>
        <input
          type="text"
          class="form-control"
          v-model="form.username"
          name="username"
          id="username"
          placeholder="Username">
      </div>
      <div class="form-group">
        <label for="user-password">Password</label>
        <input
          type="password"
          class="form-control"
          v-model="form.password"
          name="password"
          id="user-password"
          placeholder="Password">
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
    <button class="btn btn-primary my-2" @click="testConnexion">Test connection</button>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      form: {
        username: 'neilrichter',
        email: 'me@neilrichter.com',
        password: 'azeazeaze',
        passwordConfirmation: 'azeazeaze',
      },
    };
  },
  methods: {
    async login() {
      const { username, password } = this.form;
      const token = await axios.post('http://ticket-manager.ml/login', {
        username,
        password,
      })
        .then(({ data }) => data.token)
        .catch(err => console.log(err.response.data)); // eslint-disable-line

      this.$store.dispatch('setToken', token);
    },
    testConnexion() {
      // debugger; // eslint-disable-line
      const token = this.$store.state.AUTH_TOKEN;

      return axios.get('http://ticket-manager.ml/test', {
        headers: {
          'X-AUTH-TOKEN': token,
        },
      })
      .then(({ data }) => console.log(data)) // eslint-disable-line
      .catch(err => console.log(err.response)); // eslint-disable-line
    },
  },
  components: {},
};
</script>

<style lang="scss" scoped>
.login {
  .form-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
