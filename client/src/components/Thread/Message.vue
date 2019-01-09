<template>
  <div class="message">
    <div class="bar">
      <div class="group">
        {{ message.author }} {{ translations.AUTHOR_SAID }}
      </div>
      <div class="group">
        {{ translations.ANSWER_POSTED_AT }} {{ message.posted | moment('DD/MM/YYYY HH:mm')}}
      </div>
    </div>
    <div class="content" :class="{'less-padding': $store.state.GRADE === 'admin' && identifier}">
      <p v-for="(paragraph, index) in message.content.split('\n')" :key="index">
        {{ paragraph }}
      </p>
    </div>
    <div class="footer" v-if="$store.state.GRADE === 'admin' && identifier">
      <EditButton @click="editMessage"/>
      <TrashButton @click="popupOpen = true"/>
    </div>
    <ConfirmPopup
      :message="translations.DELETE_THIS_MESSAGE"
      :open="popupOpen"
      @confirm="deleteMessage"
      @close="popupOpen = false"/>
  </div>
</template>

<script>
import TrashButton from '@/components/Input/TrashButton.vue';
import EditButton from '@/components/Input/EditButton.vue';
import ConfirmPopup from '@/components/Popup/Confirm.vue';

export default {
  components: {
    TrashButton,
    EditButton,
    ConfirmPopup,
  },
  data() {
    return {
      popupOpen: false,
    };
  },
  name: 'ThreadMessage',
  props: ['message', 'identifier'],
  methods: {
    deleteMessage() {
      this.$api.delete(`http://ticket-manager.ml/tickets/${this.identifier}/message/${this.message.id}`)
        .then(() => this.$emit('deleted'))
        .catch(err => console.log(err)); // eslint-disable-line
    },
    editMessage() {
      this.$emit('edit');
    },
  },
};
</script>

<style lang="scss" scoped>
  .message {
    border: solid 1px $flatBlack;
    border-radius: 5px;
    margin: 15px 0;

    > .bar {
      @include d-flex-centered(space-between);
      border-bottom: solid 1px $flatBlack;
      padding: 10px 0;
      background-color: rgba($flatPurple, .7);
      color: #fff;

      > .group {
        margin: 0 10px;
      }
    }

    > .content {
      padding: 20px;
      text-align: left;

      &.less-padding {
        padding: 20px 20px 0;
      }
    }

    > .footer {
      padding: 0 20px 20px;
      visibility: hidden;
      @include d-flex-centered(flex-end);
    }

    &:hover {
      > .footer {
        visibility: visible;
      }
    }
  }
</style>
