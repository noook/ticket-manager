<template>
  <div class="ticket-detail">
    <h1>{{ translations.REQUEST_STATUS }}</h1>
    <div class="summary" v-if="loaded">
      <div class="group">
        {{ translations.TICKET_IDENTIFIER }}:
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
          :open="popups.deleteParticipant"
          @confirm="deleteParticipant"
          @close="popups.deleteParticipant = false"/>
      </ul>
    </div>
    <img src="@/assets/svg/loading.svg" alt="Loading" v-else>
    <hr v-if="loaded">
    <section class="thread" v-if="loaded">
      <div class="ticket-info">
        <h2>{{ ticket.title }}</h2>
        <div class="actions" v-if="$store.state.GRADE === 'admin'">
          <div
            class="button green"
            @click="updateStatus('open')"
            v-if="ticket.status === 'closed'">
            {{ translations.TICKET_ACTION_REOPEN }}
          </div>
          <div
            class="button red"
            @click="updateStatus('closed')"
            v-if="ticket.status !== 'closed'">
            {{ translations.TICKET_ACTION_CLOSE }}
          </div>
          <div
          @click="$router.push({ name: 'ticket-edit', params: { identifier: ticket.identifier }})"
            class="button blue">
            {{ translations.EDIT }}
          </div>
          <div
            @click="popups.deleteTicket = true"
            class="button red">
            {{ translations.DELETE }}
          </div>
          <ConfirmPopup
            :message="translations.DELETE_THIS_TICKET"
            :open="popups.deleteTicket"
            @confirm="deleteTicket"
            @close="popups.deleteTicket = false"/>
        </div>
      </div>
      <Message
        v-for="(item, index) in messages"
        @edit="editMessage(item)"
        @deleted="deleteMessage(item)"
        :key="index"
        :identifier="ticket.identifier"
        :message="item"/>
    </section>
    <section class="actions" v-if="loaded" v-show="ticket.status != 'closed'">
      <div class="new-message">
        <h2>{{ translations.ADD_A_MESSAGE }}:</h2>
        <form @submit.prevent="submitMessage">
          <textarea v-model="newMessage"></textarea>
          <button
            type="submit"
            :class="{ disabled: !newMessage.length }"
            :disabled="!newMessage.length">{{ translations.SUBMIT_FORM }}</button>
        </form>
      </div>
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
      popups: {
        deleteParticipant: false,
        deleteTicket: false,
      },
      confirmMessage: '',
      toDelete: null,
    };
  },
  methods: {
    confirmParticipantDelete(username) {
      this.confirmMessage = this.translations.params('CONFIRM_PARTICIPANT_DELETION', { username });
      this.toDelete = username;
      this.popups.deleteParticipant = true;
    },
    fetchTicket() {
      return this.$api.get(`/tickets/${this.$route.params.id}`)
        .then(response => response.data)
        .catch(err => console.log(err)); // eslint-disable-line
    },
    async submitMessage() {
      await this.$api.post(`/tickets/${this.$route.params.id}/new-message`, {
        message: this.newMessage,
      })
        .then(({ data }) => {
          this.messages.push(data.message);
          this.ticket.status = data.ticket.status;
        })
        .catch(err => console.log(err)); // eslint-disable-line
      this.newMessage = '';
    },
    editMessage(message) {
      this.$router.push({
        name: 'message-edit',
        params: {
          identifier: this.ticket.identifier,
          id: message.id,
        },
      });
    },
    async updateStatus(status) {
      const { newStatus } = await this.$api.put(`/tickets/${this.ticket.identifier}/status`, {
        status,
      })
        .then(({ data }) => data)
        .catch(err => console.log(err)); // eslint-disable-line
      this.ticket.status = newStatus;
    },
    deleteTicket() {
      this.$api.delete(`/tickets/${this.ticket.identifier}/delete`)
        .then(() => this.$router.push({ name: 'tickets' }))
        .catch(err => console.log(err)); // eslint-disable-line
    },
    deleteMessage(message) {
      this.messages.splice(this.messages.indexOf(message), 1);
    },
    async deleteParticipant() {
      const { participant } = await this.$api.delete(`/tickets/${this.$route.params.id}/remove-participant`, {
        data: {
          participant: this.toDelete,
        },
      })
        .then(response => response.data)
        .catch(err => console.log(err)); // eslint-disable-line

      this.participants.splice(this.participants.indexOf(participant), 1);
      this.popups.deleteParticipant = false;
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
    margin: 0 auto 50px;

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

    > hr {
      margin: 30px 0;
      border: none;
      border-bottom: solid 1px rgba($flatBlack, .2);
      height: 0px;
    }

    > section {
      &.thread {
        div.ticket-info {
          @include d-flex-centered(space-between);

          > h2 {
            font-size: 1.8rem;
            text-align: left;
          }

          > .actions {
            display: flex;

            > .button {
              border-radius: 5px;
              padding: 5px 10px;
              color: #fff;

              &:hover {
                cursor: pointer;
              }

              &.blue {
                background-color: rgba($flatBlue, .9);
              }

              &.red {
                background-color: rgba($flatRed, .9);
              }

              &.green {
                background-color: rgba($flatGreen, .9);
              }

              &:not(:first-child) {
                margin-left: 10px;
              }
            }
          }
        }
      }

      &.actions {
        display: flex;

        > div {
          margin-right: 15px;
        }
        > .new-message {
          text-align: left;

          > h2 {
            font-size: 1.5rem;
          }

          > form {
            width: 100%;

            > textarea {
              display: block;
              margin: 15px 0;
              width: 400px;
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

        > .ticket-actions {
          > h2 {
            font-size: 1.5rem;
          }

          > .buttons {
            display: flex;
            padding: 5px;

            > .button {
              border-radius: 5px;
              flex: 1 0 0;
              padding: 5px 10px;
              color: #fff;
              margin: 5px;

              &.green {
                background-color: rgba($flatGreen, .9);
              }

              &.red {
                background-color: rgba($flatRed, .9);
              }
            }
          }
        }
      }
    }
  }
</style>
