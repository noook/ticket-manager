<template>
  <div class="tickets">
    <h1>{{ translations.MY_TICKETS }}</h1>
    <div class="ticket-list">
      <table v-if="loaded">
        <thead class="filters">
          <td colspan="10">
            <input type="checkbox" v-model="showClosed">
            <label>{{ translations.LIST_SHOW_CLOSED_TICKETS }}</label>
            <input type="checkbox" v-model="showAssigned">
            <label>{{ translations.LIST_SHOW_ASSIGNED_TICKETS }}</label>
          </td>
        </thead>
        <thead>
          <td>{{ translations.TICKET_IDENTIFER }}</td>
          <td>{{ translations.TICKET_AUTHOR }}</td>
          <td>{{ translations.TICKET_TITLE }}</td>
          <td>{{ translations.TICKET_STATUS }}</td>
          <td>{{ translations.TICKET_LAST_UPDATE }}</td>
        </thead>
        <tr
          v-for="(item, index) in filteredList"
          :key="index"
          @click="$router.push({ name: 'ticket-detail', params: { id: item.identifier }})">
          <td class="identifier">
            #{{ item.identifier }}
            <span class="ticket-assigned" v-if="item.assigned">
              {{ translations.ASSIGNED_TO_THIS }}
            </span>
          </td>
          <td>{{ item.author }}</td>
          <td>{{ item.title }}</td>
          <td>
            <span :class="item.status">•</span>
            {{ translations[`TICKET_STATUS_SHORT_${item.status.toUpperCase()}`] }}
          </td>
          <td>{{ item.updated | moment('DD-MM-YYYY HH:mm:ss') }}</td>
        </tr>
        <tr class="no-ticket" v-if="!filteredList.length">
          <td colspan="7">{{ translations.NO_TICKET_YET }}</td>
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
      showClosed: false,
      showAssigned: true,
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
  computed: {
    filteredList() {
      const { showClosed, showAssigned } = this;
      return this.tickets
        .filter(ticket => showClosed || ticket.status !== 'closed')
        .filter(ticket => showAssigned || !ticket.assigned);
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
        width: 80%;
        text-align: left;
        border-collapse: collapse;
        margin: auto;

        td {
          padding: 10px 20px;

          &.identifier {
            @include d-flex-centered(space-between);
          }

          > span.ticket-assigned {
            margin-left: 10px;
            font-size: .9rem;
            padding: 5px 10px;
            border-radius: 5px;
            border: solid 1px rgba($flatBlack, .5);
          }
        }

        thead {
          color: rgba($flatBlack, .4);
          background-color: #efefef;
          font-weight: 500;

          &.filters {
            background-color: inherit;

            input {
              margin: 0 10px;
            }
            label {
              margin-right: 10px;
            }
          }
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

          &.no-ticket > td {
            text-align: center;
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
