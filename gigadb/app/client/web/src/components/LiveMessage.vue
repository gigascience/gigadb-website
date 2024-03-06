<template>
  <div class="sr-only" :aria-live="politeness">
    {{ liveMessage }}
  </div>
</template>

<script>
export default {
  name: "LiveMessage",
  props: {
    message: {
      type: String,
      default: ""
    },
    politeness: {
      type: String,
      default: "polite",
      validator: function (value) {
        return ["off", "polite", "assertive"].includes(value);
      }
    }
  },
  data() {
    return {
      liveMessage: this.message,
      timer: null,
    }
  },
  beforeDestroy() {
    this.timer && clearTimeout(this.timer);
  },
  watch: {
    message(newMessage) {
      // set to emtpy first so that live region sees a change
      this.liveMessage = "";

      clearTimeout(this.timer);
      this.timer = setTimeout(() => {
        this.liveMessage = newMessage;
      },
        500 // sufficient delay to ensure that the message is read out timely
      );
    }
  }
}
</script>