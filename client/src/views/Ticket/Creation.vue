<template>
  <div class="ticket-creation">
    <h1>{{ translations.NEW_TICKET }}</h1>
    <form @submit.prevent="submitTicket">
      <div class="group">
        <label for="ticket-subject">{{ translations.TICKET_SUBJECT }}:</label>
        <input
          type="text"
          v-model="subject"
          name="ticket-subject"
          id="ticket-subject">
      </div>
      <div class="group">
        <label for="ticket-content">{{ translations.TICKET_MESSAGE }}:</label>
        <textarea
          v-model="content"
          name="ticket-content"
          id="ticket-content"></textarea>
      </div>
      <div class="group">
        <button type="submit">{{ translations.SUBMIT_FORM }}</button>
      </div>
    </form>
  </div>
</template>

<script>
export default {
  name: 'TicketCreation',
  data() {
    return {
      subject: null,
      content: null,
    };
  },
  methods: {
    submitTicket() {
      this.$api.post('/tickets/new', {
        subject: this.subject,
        content: this.content,
      })
        .then((response) => {
          this.$router.push({
            name: 'ticket-detail',
            params: {
              id: response.data.identifier,
            },
          });
        })
        .catch(err => console.log(err)); // eslint-disable-line
    },
  },
};
</script>

<style lang="scss" scoped>
  .ticket-creation {
    width: 50%;
    margin: auto;

    > h1 {
      font-size: 2rem;
    }

    > form {
      > .group {
        display: flex;
        flex-direction: column;
        align-items: flex-start;
        margin: 10px 0;

        > input, {
          all: inherit;
          width: 100%;
          border-radius: 5px;
          padding: 5px 10px;
          outline: none;
          border: solid 1px rgba($flatBlack, .5);
          margin: 5px 0;
          text-align: left;
        }

        > textarea {
          min-height: 200px;
          padding: 10px;
          width: 100%;
          border-radius: 5px;
          padding: 5px 10px;
          outline: none;
          border: solid 1px rgba($flatBlack, .5);
          margin: 5px 0;
          text-align: left;
          resize: none;
          font-family: $defaultFont;
        }

        > button {
          all: inherit;
          padding: 5px 10px;
          background-color: $flatGreen;
          border-radius: 5px;
          color: #fff;
          margin: auto;
          font-size: 1.2rem;

          &:hover {
            cursor: pointer;
          }
        }
      }
    }
  }
</style>
