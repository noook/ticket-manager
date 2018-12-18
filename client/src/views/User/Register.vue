<template>
  <div class="register">
    <form class="container w-25" @submit.prevent="register">
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
        <label for="email-adress">Email address</label>
        <input
          type="email"
          class="form-control"
          v-model="form.email"
          name="email"
          id="email-adress"
          placeholder="Enter email">
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
      <div class="form-group">
        <label for="user-password-confirmation">Password confirmation</label>
        <input
          type="password"
          class="form-control"
          v-model="form.passwordConfirmation"
          name="password-confirmation"
          id="user-password-confirmation"
          placeholder="Confirm password">
      </div>
      <button type="submit" class="btn btn-primary">Submit</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'register',
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
    register() {
      // console.log(this.$store);
      const { username, email, password } = this.form;
      console.log(username, email, password);
      axios.post('https://ticket-manager.ml/register', {
        username,
        email,
        password,
      })
      .then(({ data }) => console.log(data)) // eslint-disable-line
      .catch(err => console.log(err)); // eslint-disable-line
    },
  },
  components: {},
};
</script>

<style lang="scss" scoped>
.register {
  .form-group {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
  }
}
</style>
