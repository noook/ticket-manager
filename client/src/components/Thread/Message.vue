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
    <div class="content">
      <p v-for="(paragraph, index) in message.content.split('\n')" :key="index">
        {{ paragraph }}
      </p>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ThreadMessage',
  props: ['message'],
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
    }
  }
</style>
