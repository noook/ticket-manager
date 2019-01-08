<template>
  <div class="message-edit">
    <h1>{{ translations.MESSAGE_EDITION }}</h1>
    <div class="content" v-if="loaded">
      <h2>
          {{ translations.TICKET }}
          <router-link :to="linkTo">#{{ $route.params.identifier }}</router-link>:
          {{ ticket.title }}</h2>
      <h3>{{ translations.MESSAGE_TO_EDIT }}:</h3>
      <Message :message="message" />
      <section class="message-edition">
        <h3>{{ translations.EDIT_HERE }}:</h3>
        <form @submit.prevent="editMessage">
          <textarea v-model="editedText"></textarea>
          <button type="submit">{{ translations.SAVE }}</button>
        </form>
      </section>
    </div>
  </div>
</template>

<script>
import Message from '@/components/Thread/Message.vue';

export default {
  name: 'MessageEdit',
  created() {
    this.fetchMessageInfos()
      .then(({ message, ticket }) => {
        this.message = message;
        this.ticket = ticket;
        this.editedText = message.content;
        this.loaded = true;
      })
      .catch(err => console.log(err)); // eslint-disable-line
  },
  data() {
    return {
      loaded: false,
      message: null,
      ticket: null,
      linkTo: {
        name: 'ticket-detail',
        params: {
          id: this.$route.params.identifier,
        },
      },
      editedText: null,
    };
  },
  components: {
    Message,
  },
  methods: {
    fetchMessageInfos() {
      const { identifier, id } = this.$route.params;
      return this.$api.get(`http://ticket-manager.ml/tickets/${identifier}/message/${id}`)
        .then(({ data }) => data)
        .catch(err => console.log(err)); // eslint-disable-line
    },
    editMessage() {
      const { identifier, id } = this.$route.params;
      this.$api.put(`http://ticket-manager.ml/tickets/${identifier}/message/${id}`, {
        text: this.editedText,
      })
        .then(() => {
          this.$router.push({
            name: 'ticket-detail',
            params: {
              id: identifier,
            },
          });
        })
        .catch(err => console.log(err)); // eslint-disable-line
    },
  },
};
</script>

<style lang="scss" scoped>
  .message-edit {
    width: 70%;
    margin: auto;

    > h1 {
      font-size: 2rem;
      margin: 20px 0;
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
        margin-top: 10px;
      }

      > section {
        > form {
          width: 500px;

          > textarea {
            display: block;
            font-family: $defaultFont;
            font-weight: 400;
            width: 100%;
            padding: 10px;
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
