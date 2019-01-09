<template>
  <div class="confirm-popup" v-if="open">
    <div class="blur"></div>
    <div class="popup-box">
      <div class="content-row">
        {{ message }}
      </div>
      <div class="footer-row">
        <div class="cancel" @click="$emit('close')">{{ cancelText }}</div>
        <div class="confirm" @click="$emit('confirm')">{{ confirmText }}</div>
      </div>
    </div>
  </div>
</template>

<script>
export default {
  name: 'ConfirmPopup',
  props: {
    open: {
      type: Boolean,
      required: true,
    },
    message: {
      type: String,
      required: true,
    },
    confirmText: {
      default() {
        return this.$parent.translations.CONFIRM;
      },
      type: String,
    },
    cancelText: {
      default() {
        return this.$parent.translations.CANCEL;
      },
      type: String,
    },
  },
};
</script>

<style lang="scss" scoped>
  .confirm-popup {
    position: fixed;
    top: 0;
    right: 0;
    left: 0;
    bottom: 0;
    @include d-flex-centered(center);

    > .blur {
      position: absolute;
      top: 0;
      right: 0;
      left: 0;
      bottom: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(#000, .7);
    }

    > .popup-box {
      margin-bottom: 100px;
      background-color: #fff;
      border-radius: 5px;
      padding: 15px 25px;
      z-index: 50;
      border: solid 1px rgba($flatBlack, .5);

      > .close-row {
        @include d-flex-centered(flex-end);

        > img {
          width: 12px;
          height: 12px;
          fill: $flatRed;
          font-size: 1.4rem;
          &:hover {
            cursor: pointer;
          }
        }
      }

      > .content-row {
        text-align: justify;
        width: 300px;
        margin: 20px 0;
      }

      > .footer-row {
        @include d-flex-centered(space-between);

        > div {
          padding: 5px 15px;
          width: 40%;
          text-align: center;
          margin: 0 5px;
          border-radius: 5px;
          color: #fff;

          &:hover {
            cursor: pointer;
          }

          &.cancel {
            background-color: $flatRed;
          }

          &.confirm {
            background-color: $flatGreen;
          }
        }
      }
    }
  }
</style>
