<template>
  <div class="login">
    <form @submit.prevent="login">
      <h2>{{ translations.LOGIN }}</h2>
      <div class="login-inputs">
        <input
          type="text"
          v-model="form.username"
          name="username"
          id="username"
          :placeholder="translations.FORM_USERNAME">
        <input
          type="password"
          v-model="form.password"
          name="password"
          id="user-password"
          :placeholder="translations.FORM_PASSWORD">
          <small v-if="loginError">{{ translations.INVALID_USERNAME_OR_PASSWORD }}</small>
      </div>
      <button type="submit" class="login-submit">{{ translations.LOGIN }}</button>
    </form>
  </div>
</template>

<script>
import axios from 'axios';

export default {
  name: 'Login',
  data() {
    return {
      form: {
        username: null,
        email: null,
        password: null,
        passwordConfirmation: null,
      },
      loginError: false,
    };
  },
  methods: {
    async login() {
      this.loginError = false;
      const { username, password } = this.form;
      const { token, expiracy } = await axios.post('http://ticket-manager.ml/login', {
        username,
        password,
      })
        .then(({ data }) => data)
        .catch(() => { this.loginError = true; });

      this.$store.dispatch('setToken', { token, expiracy });
      this.$router.push('/tickets');
    },
  },
  components: {},
};
</script>

<style lang="scss" scoped>
.login {
  > form {
    > h2 {
      font-size: 2rem;
      margin-bottom: 15px;
    }

    > .login-inputs {
      display: flex;
      flex-direction: column;
      margin: 15px 0;

      > input {
        all: inherit;
        border-radius: 20px;
        background-color: #f5f5f5;
        padding: 5px 20px;
        margin: 5px 0;
        text-align: left;
      }

      > small {
        text-align: left;
        color: #d91e18;
        padding: 0 5px;
      }
    }

    > .login-submit {
      all: inherit;
      background-color: #2ECC71;
      box-sizing: border-box;
      border-radius: 20px;
      padding: 5px 20px;
      color: #fff;
      width: 100%;
      font-weight: bold;

      &:hover {
        cursor: pointer;
      }
    }
  }
}
</style>
