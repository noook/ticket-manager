<template>
  <div class="admin-user-list">
    <h1>{{ translations.USERS }}:</h1>
    <div class="save-actions">
      <div
        @click="update"
        class="save-button">{{ translations.SAVE }}</div>
      <div :class="{ show: saved }" class="success">
        <img src="@/assets/svg/check-green.svg" alt="Green check">
        {{ translations.SAVED }}</div>
    </div>
    <table v-if="loaded">
      <thead>
        <td>{{ translations.TICKET_IDENTIFIER }}</td>
        <td>{{ translations.FORM_USERNAME }}</td>
        <td>{{ translations.FORM_EMAIL }}</td>
        <td
          v-for="(role, index) in roles"
          :key="index">{{ translations[role] }}</td>
      </thead>
      <tr v-for="(user, index) in users" :key="index">
        <td>{{ user.id }}</td>
        <td>{{ user.username }}</td>
        <td>{{ user.email }}</td>
        <td class="center" v-for="(role, index) in roles" :key="index">
          <input
            type="checkbox"
            :disabled="$store.state.USER === user.username"
            :checked="user.roles.includes(role)"
            @click="toggle(role, user)">
        </td>
      </tr>
    </table>
  </div>
</template>

<script>
/* eslint-disable no-loop-func */
export default {
  name: 'AdminUserList',
  data() {
    return {
      loaded: false,
      saved: false,
      users: [],
      roles: [],
      original: [],
      changed: {},
    };
  },
  async created() {
    const { roles, users } = await this.$api.get('/users')
      .then(({ data }) => data)
      .catch(err => console.log(err)); // eslint-disable-line

    users.forEach((user) => {
      user.roles = user.roles.filter(role => role !== 'ROLE_USER'); // eslint-disable-line
      this.changed[user.id] = {
        added: [],
        deleted: [],
      };
    });

    this.users = users;
    this.users.sort((a, b) => a.username > b.username);
    this.original = JSON.parse(JSON.stringify(users));
    this.roles = roles;
    this.loaded = true;
  },
  methods: {
    toggle(role, user) {
      if (user.roles.includes(role)) {
        if (this.changed[user.id].added.includes(role)) {
          this.changed[user.id].added = this.changed[user.id].added.filter(el => el !== role);
        }
        this.changed[user.id].deleted.push(role);
        user.roles.splice(user.roles.indexOf(role), 1);
      } else {
        if (this.changed[user.id].deleted.includes(role)) {
          this.changed[user.id].deleted = this.changed[user.id].deleted.filter(el => el !== role);
        }
        this.changed[user.id].added.push(role);
        user.roles.push(role);
      }
      user.roles.sort();
    },
    async update() {
      const payload = {};
      this.users.forEach((user) => {
        payload[user.id] = user.roles;
      });

      await this.$api.put('/users/update/roles', {
        payload,
      })
        .catch(err => console.log(err)); // eslint-disable-line
      this.saved = true;
      setTimeout(() => {
        this.saved = false;
      }, 4000);
    },
  },
};
</script>

<style lang="scss" scoped>
  .admin-user-list {
    display: flex;
    flex-direction: column;
    align-items: flex-start;
    width: 70%;
    margin: auto;

    > h1 {
      font-size: 2rem;
      text-align: left;
    }

    > .save-actions {
      @include d-flex-centered(flex-start);

      > .success {
        display: none;
        @keyframes fade {
          0%   { opacity: 1; }
          100% { opacity: 0; }
        }

        img {
          width: 16px;
          height: 16px;
          margin-right: 5px;
        }

        &.show {
          @include d-flex-centered(flex-start);
          display: block;
          animation: fade 3s 1s;
          animation-fill-mode: forwards;
        }
      }

      > .save-button {
        display: inline;
        margin: 10px 0;
        border-radius: 5px;
        padding: 5px 10px;
        margin-right: 15px;
        background-color: rgba($flatGreen, .9);
        color: #fff;

        &:hover {
          cursor: pointer;
        }
      }
    }

    > table {
      border-collapse: collapse;
      > tr, thead {
        > td {
          padding: 10px;
          text-align: left;

          &.center {
            text-align: center;
          }
        }
      }

      > thead {
        background-color: #efefef;
        border-bottom: solid 1px rgba($flatBlack, .3);
      }

      > tr {
        &:nth-child(odd) {
          background-color: #f4f4f4;
        }
        &:nth-child(even) {
          background-color: #fff;
        }
      }
    }
  }
</style>
