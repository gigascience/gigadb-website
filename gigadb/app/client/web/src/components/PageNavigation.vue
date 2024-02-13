<template>
  <nav class="text-right">
    <div
      class="button-div"
      v-if="stage === 'uploading' && (uploadsComplete === true || (!isNaN(numUploadsExist) && numUploadsExist > 0))"
    >
      <a :href="annotationUrl" class="btn background-btn nav-link">Next (Metadata Form)</a>
    </div>
    <div v-if="stage === 'annotating' && metadataComplete === true">
      <button class="btn background-btn complete complete-btn" type="submit">
        Complete and return to Your Uploaded
        Datasets page
      </button>
    </div>
  </nav>
</template>

<style scoped>
.nav-link {
  margin: 5px;
  width: 30%;
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