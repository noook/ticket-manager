<template>
  <div class="tickets">
    <h1>{{ translations.MY_TICKETS }}</h1>
    <div class="ticket-list">
      <table v-if="loaded">
        <thead>
          <td>{{ translations.TICKET_IDENTIFER }}</td>
          <td>{{ translations.TICKET_TITLE }}</td>
          <td>{{ translations.TICKET_STATUS }}</td>
          <td>{{ translations.TICKET_LAST_UPDATE }}</td>
        </thead>
        <tr
          v-for="(item, index) in tickets"
          :key="index"
          @click="$router.push({ name: 'ticket-detail', params: { id: item.identifier }})">
          <td>#{{ item.identifier }}</td>
          <td>{{ item.title }}</td>
          <td>
            <span :class="item.status">•</span>
            {{ translations[`TICKET_STATUS_${item.status.toUpperCase()}`] }}
          </td>
          <td>{{ item.updated | moment('DD-MM-YYYY') }}</td>
        </tr>
      </table>
      <img src="@/assets/svg/loading.svg" alt="Loading" v-else>
    </div>
  </div>
</template>

<script>
import moment from 'moment';

export default {
  name: 'TicketsList',
  data() {
    return {
      tickets: [],
      loaded: false,
    };
  },
  created() {
    this.fetchTickets()
      .then((data) => {
        this.tickets = data.map(ticket => ({
          ...ticket,
          created: moment(ticket.created),
          updated: moment(ticket.updated),
        }));
        this.loaded = true;
      })
      .catch(err => console.log(err)); // eslint-disable-line
  },
  methods: {
    fetchTickets() {
      return this.$api.get('http://ticket-manager.ml/tickets')
        .then(response => response.data)
        .catch(err => console.log(err)); // eslint-disable-line
    },
  },
};
</script>


<style lang="scss" scoped>
  .tickets {
    font-size: 2.5rem;

    > .ticket-list {
      margin: 50px 100px;

      > table {
        font-size: 1.2rem;
        text-align: left;
        border-collapse: collapse;
        margin: auto;

        td {
          padding: 10px 20px;
        }

        thead {
          color: rgba($flatBlack, .4);
          background-color: #efefef;
          font-weight: 500;
        }

        > tr {
          &:not(.spacer) {
            &:nth-child(odd) {
              background-color: #f4f4f4;
            }
            &:nth-child(even) {
              background-color: #fff;
            }

            &:hover {
              background-color: #f2f2f2;
              cursor: pointer;
            }
          }

          > td {
             > span {

              &.open {
                color: $flatGreen;
              }

              &.closed {
                color: $flatRed;
              }

              &.awaiting {
                color: $flatPurple;
              }
            }
          }
        }
      }
    }
  }
</style>