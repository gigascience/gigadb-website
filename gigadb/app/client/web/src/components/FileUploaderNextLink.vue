<template>
  <div>
    <a :href="annotationUrl" :class="['btn nav-link', areUploadsComplete ? 'background-btn' : 'disabled-btn']"
      :aria-disabled="!areUploadsComplete ? 'true' : 'false'" @click="handleClick">
      {{ linkLabel }}
    </a>
  </div>
</template>

<style scoped>
.nav-link {
  width: 100%;
}

.complete-btn {
  margin: 5px;
}
</style>

<script>
import { eventBus } from "../index.js";

export default {
  props: {
    identifier: {
      type: String,
      required: true,
    },
    uploadsExist: {
      type: String,
    },
  },
  data: function () {
    return {
      uploadsComplete: false,
      annotationUrl: `/authorisedDataset/annotateFiles/id/${this.identifier}`,
      linkLabel: 'Next (Metadata Form)'
    };
  },
  computed: {
    numUploadsExist() {
      return Number(this.uploadsExist);
    },
    areUploadsComplete() {
      return (this.uploadsComplete === true || (!isNaN(this.numUploadsExist) && this.numUploadsExist > 0))
    }
  },
  methods: {
    handleClick(event) {
      if (!this.areUploadsComplete) {
        event.preventDefault();
      }
    },
  },
  mounted: function () {
    eventBus.$on("complete", () => {
      this.uploadsComplete = true;
    });
  },
};
</script>