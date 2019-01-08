<template>
  <div class="ticket-detail">
    <h1>{{ translations.REQUEST_STATUS }}</h1>
    <div class="summary" v-if="loaded">
      <div class="group">
        {{ translations.TICKET_IDENTIFER }}:
        <a href="#">{{ ticket.identifier }}</a>
      </div>
      <div class="group">
        {{ translations.TICKET_AUTHOR }}:
        {{ ticket.author }}
      </div>
      <div class="group">
        {{ translations.TICKET_STATUS }}:
        <span :class="ticket.status">•</span>
        {{ translations[`TICKET_STATUS_${ticket.status.toUpperCase()}`] }}
      </div>
      <div class="group">
        {{ translations.TICKET_CREATION_DATE }}:
        {{ ticket.created | moment('DD/MM/YYYY HH:mm ')}}
      </div>
      <div class="group">
        {{ translations.TICKET_LAST_UPDATE }}:
        {{ latestMessage.posted | moment('DD/MM/YYYY HH:mm ')}}
      </div>
    </div>
    <div class="admin-panel" v-if="$store.state.GRADE === 'admin' && loaded">
      <UserSearch :participants.sync="participants"/>
    </div>
    <div class="participants" v-if="loaded" v-show="participants.length">
      <h2>{{ translations.PARTICIPATING }}:</h2>
      <ul>
        <li v-for="(item, index) in participants" :key="index">
          {{ item }}
          <img
            v-if="$store.state.GRADE === 'admin'"
            @click="confirmParticipantDelete(item)"
            src="@/assets/svg/close.svg"/>
        </li>
        <ConfirmPopup
          :message="confirmMessage"
          :open="popupOpen"
          @confirm="deleteParticipant"
          @close="popupOpen = false"/>
      </ul>
    </div>
    <img src="@/assets/svg/loading.svg" alt="Loading" v-else>
    <section class="thread">
      <Message
        v-for="(item, index) in messages"
        @deleted="deleteMessage(item)"
        :key="index"
        :message="item"/>
    </section>
    <section class="new-message" v-if="loaded" v-show="ticket.status != 'closed'">
      <h2>{{ translations.ADD_A_MESSAGE }}:</h2>
      <form @submit.prevent="submitMessage">
        <textarea v-model="newMessage"></textarea>
        <button
          type="submit"
          :class="{ disabled: !newMessage.length }"
          :disabled="!newMessage.length">{{ translations.SUBMIT_FORM }}</button>
      </form>
    </section>
  </div>
</template>

<script>
import Message from '@/components/Thread/Message.vue';
import ConfirmPopup from '@/components/Popup/Confirm.vue';
import UserSearch from '@/components/Input/UserSearch.vue';

export default {
  name: 'Ticketdetail',
  components: {
    Message,
    UserSearch,
    ConfirmPopup,
  },
  async created() {
    const { ticket, messages, participants } = await this.fetchTicket();
    this.messages = messages;
    this.ticket = ticket;
    this.participants = participants;
    this.loaded = true;
  },
  data() {
    return {
      loaded: false,
      ticket: null,
      messages: [],
      participants: [],
      newMessage: '',
      popupOpen: false,
      confirmMessage: '',
      toDelete: null,
    };
  },
  methods: {
    confirmParticipantDelete(username) {
      this.confirmMessage = this.translations.params('CONFIRM_PARTICIPANT_DELETION', { username });
      this.toDelete = username;
      this.popupOpen = true;
    },
    fetchTicket() {
      return this.$api.get(`http://ticket-manager.ml/tickets/${this.$route.params.id}`)
        .then(response => response.data)
        .catch(err => console.log(err)); // eslint-disable-line
    },
    async submitMessage() {
      await this.$api.post(`http://ticket-manager.ml/tickets/${this.$route.params.id}/new-message`, {
        message: this.newMessage,
      })
        .then(({ data }) => {
          this.messages.push(data.message);
          this.ticket.status = data.ticket.status;
        })
        .catch(err => console.log(err)); // eslint-disable-line
      this.newMessage = '';
    },
    deleteMessage(message) {
      this.messages.splice(this.messages.indexOf(message), 1);
    },
    async deleteParticipant() {
      const { participant } = await this.$api.delete(`http://ticket-manager.ml/tickets/${this.$route.params.id}/remove-participant`, {
        data: {
          participant: this.toDelete,
        },
      })
        .then(response => response.data)
        .catch(err => console.log(err)); // eslint-disable-line

      this.participants.splice(this.participants.indexOf(participant), 1);
      this.popupOpen = false;
    },
  },
  computed: {
    latestMessage() {
      return this.messages.concat().reverse()[0];
    },
  },
};
</script>


<style lang="scss" scoped>
  .ticket-detail {
    width: 60%;
    margin: auto;

    > h1 {
      font-size: 2rem;
      margin: 20px 0;
    }

    > .summary {
      @include d-flex-centered(flex-start);
      padding: 10px 0;

      > .group {
        margin: 0 10px;

        > a {
          color: #42A7FF;
        }

        > span {
          &.closed {
            color: $flatRed;
          }
          &.open {
            color: $flatGreen;
          }
          &.awaiting {
            color: $flatPurple;
          }
        }
      }
    }

    > .participants {
      @include d-flex-centered(flex-start);
      text-align: left;
      padding: 0 10px;

      > h2 {
        font-size: 1.2rem;
        display: inline;
      }

      > ul {
        @include d-flex-centered(flex-start);
        margin: 0 10px;

        > li {
          padding: 5px 10px;
          border-radius: 5px;
          border: solid 1px rgba($flatBlack, .5);
          margin: 0 5px;
          position: relative;

          > img {
            height: 8px;
            width: 8px;
            margin-left: 5px;
            visibility: hidden;
            top: -6px;
            right: -6px;
            background-color: $flatRed;
            padding: 4px;
            border-radius: 100%;
            position: absolute;

            &:hover {
              cursor: pointer;
            }
          }

          &:hover {
            > img {
              visibility: visible;
            }
          }
        }
      }
    }

    > section {
      &.new-message {
        text-align: left;

        > h2 {
          font-size: 1.5rem;
        }

        > form {
          width: 50%;

          > textarea {
            display: block;
            margin: 15px 0;
            width: 100%;
            border-radius: 5px;
            min-height: 100px;
            box-sizing: border-box;
            padding: 10px;
            outline: none;
            resize: none;
            font-family: $defaultFont;
            border: solid 1px rgba($flatBlack, .5);
          }

          > button {
            all: inherit;
            width: auto;
            display: block;
            padding: 5px 10px;
            background-color: $flatGreen;
            border-radius: 5px;
            outline: none;
            color: #fff;

            &.disabled {
              background-color: rgba($flatBlack, .2);
            }
          }
        }
      }
    }
  }
</style>
