<template>
  <div class="user-search">
    <h3>{{ translations.ADD_PARTICIPANTS }}:</h3>
    <div class="search-box">
      <input
        type="text"
        v-model="query">
      <div v-if="query != ''" >
        <p v-for="(user, index) in queryResults" :key="index">
          {{ user.username }}
          <span @click="addParticipant(user.id)">+</span>
        </p>
      </div>
    </div>
  </div>
</template>

<script>
import { debounce } from 'lodash';

export default {
  name: 'UserSearch',
  props: ['participants'],
  data() {
    return {
      query: null,
      queryResults: [],
    };
  },
  created() {
    this.debouncedSearch = debounce(this.searchUser, 500);
  },
  methods: {
    async searchUser() {
      let queryResults = await this.$api.get('http://ticket-manager.ml/users/search', {
        params: {
          query: this.query,
        },
      })
        .then(response => response.data.users)
        .catch(err => console.log(err)); // eslint-disable-line
      const { participants } = this;
      const ticketAuthor = this.$parent.ticket.author;
      queryResults = queryResults
        .filter(user => !participants.includes(user.username) && user.username !== ticketAuthor);
      this.queryResults = queryResults;
    },
    async addParticipant(id) {
      const { participant } = await this.$api.post(`http://ticket-manager.ml/tickets/${this.$parent.$route.params.id}/add-participant`, {
        participant: id,
      })
        .then(response => response.data)
        .catch(err => console.log(err)); // eslint-disable-line
      // console.log(participant);
      this.participants.push(participant);
      this.query = '';
    },
  },
  watch: {
    query() {
      if (this.query !== '') {
        this.debouncedSearch();
      }
    },
  },
};
</script>

<style lang="scss" scoped>
  .user-search {
    margin: 10px 0;
    > h3 {
      text-align: left;
      display: block;
      font-size: 1.3rem;
    }
    > .search-box {
      margin: 5px 0;
      @include d-flex-centered(flex-start);

      > input {
        margin-right: 15px;
      }

      > div p {
        padding: 2px 10px;
        margin: 0 5px;
        border-radius: 5px;
        border: solid 1px rgba($flatBlack, .5);

        > span {
          margin-left: 10px;
          color: $flatBlue;
          // font-size: 1.2rem;
          font-weight: bold;

          &:hover {
            cursor: pointer;
          }
        }
      }
    }
  }
</style>
