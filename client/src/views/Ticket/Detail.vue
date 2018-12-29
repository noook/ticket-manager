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
        <span :class="ticket.status">•</span>{{ ticket.status }}
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
    <section class="thread">
      <Message
        v-for="(item, index) in messages"
        :key="index"
        :message="item"/>
    </section>
  </div>
</template>

<script>
import Message from '@/components/Thread/Message.vue';

export default {
  name: 'Ticketdetail',
  components: {
    Message,
  },
  async created() {
    const { ticket, messages } = await this.fetchTicket();
    this.messages = messages;
    this.ticket = ticket;
    this.loaded = true;
  },
  data() {
    return {
      loaded: false,
      ticket: null,
      messages: [],
    };
  },
  methods: {
    fetchTicket() {
      return this.$api.get(`http://ticket-manager.ml/tickets/${this.$route.params.id}`)
        .then(response => response.data)
        .catch(err => console.log(err)); // eslint-disable-line
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
    width: 70%;
    margin: auto;

    > h1 {
      font-size: 2rem;
    }

    > .summary {
      @include d-flex-centered(flex-start);
      margin: 20px 0;
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
  }
</style>
