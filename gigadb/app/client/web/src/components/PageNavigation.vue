<template>
  <div :class="{'text-right': showCompleteBtn}">
    <div v-if="showNextBtn">
      <a :href="annotationUrl" class="btn background-btn nav-link">Next (Metadata Form)</a>
    </div>
    <div v-if="showCompleteBtn">
      <button class="btn background-btn complete complete-btn" type="submit">
        Complete and return to Your Uploaded
        Datasets page
      </button>
    </div>
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
      stage: "undetermined",
      uploadsComplete: false,
      metadataComplete: false,
      annotationUrl: `/authorisedDataset/annotateFiles/id/${this.identifier}`,
    };
  },
  computed: {
    numUploadsExist() {
      return Number(this.uploadsExist);
    },
    showCompleteBtn() {
      return this.stage === "annotating" && this.metadataComplete === true;
    },
    showNextBtn() {
      return this.stage === 'uploading' && (this.uploadsComplete === true || (!isNaN(this.numUploadsExist) && this.numUploadsExist > 0))
    }
  },
  mounted: function () {
    eventBus.$on("stage-changed", (stage) => {
      this.stage = stage;
    });
    eventBus.$on("complete", () => {
      this.uploadsComplete = true;
    });
    eventBus.$on("metadata-ready-status", (status) => {
      this.metadataComplete = status;
    });
  },
};
</script>