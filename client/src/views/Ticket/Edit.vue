<template>
  <div class="ticket-edit">
    <h1>{{ translations.TICKET_EDITION }}</h1>
    <div class="content" v-if="loaded">
      <h2>
          {{ translations.TICKET }}
          <router-link :to="linkTo">#{{ $route.params.identifier }}</router-link>:
          {{ ticket.title }}</h2>
      <section class="message-edition">
        <h3>{{ translations.EDIT_TITLE }}:</h3>
        <form @submit.prevent="updateTitle">
          <input type="text" v-model="toEdit">
          <button type="submit">{{ translations.SAVE }}</button>
        </form>
      </section>
    </div>
  </div>
</template>

<script>
export default {
  name: 'TicketEdit',
  async created() {
    const { ticket } = await this.$api.get(`/tickets/${this.$route.params.identifier}`)
      .then(({ data }) => data)
      .catch(err => console.log(err)); // eslint-disable-line
    this.ticket = ticket;
    this.toEdit = ticket.title;
    this.loaded = true;
  },
  data() {
    return {
      loaded: false,
      ticket: null,
      linkTo: {
        name: 'ticket-detail',
        params: {
          id: this.$route.params.identifier,
        },
      },
      toEdit: null,
    };
  },
  methods: {
    updateTitle() {
      this.$api.put(`/tickets/${this.ticket.identifier}/edit`, {
        content: this.toEdit,
      })
        .then(() => this.$router.push({
          name: 'ticket-detail',
          params: {
            id: this.ticket.identifier,
          },
        }))
        .catch(err => console.log(err)); // eslint-disable-line
    },
  },
};
</script>

<style lang="scss" scoped>
  .ticket-edit {
    width: 70%;
    margin: auto;
    > h1 {
      font-size: 2rem;
    }

    > .content {
      > h2 {
        font-size: 1.5rem;
        text-align: left;

        > a {
          color: $flatBlue;
        }
      }

      h3 {
        font-size: 1.3rem;
        text-align: left;
        margin: 10px 0;
      }

      > section {
        > form {
          width: 400px;

          > input {
            font-size: 1.2rem;
            display: block;
            font-family: $defaultFont;
            width: 100%;
            padding: 5px 10px;
            border-radius: 5px;
            resize: none;
            border: solid 1px rgba($flatBlack, .5);
          }

          > button {
            all: inherit;
            width: auto;
            box-sizing: border-box;
            font-family: $defaultFont;
            margin: 10px auto;
            background-color: rgba($flatGreen, .9);
            padding: 5px 10px;
            border-radius: 5px;
            color: #fff;

            &:hover {
              cursor: pointer;
            }
          }
        }
      }
    }
  }
</style>
