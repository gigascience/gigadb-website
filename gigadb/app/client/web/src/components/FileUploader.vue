<template>
  <div id="uppy">
    <form class="uppy-dataset-metadata-form">
      <input id="dataset" type="hidden" :value="identifier">
    </form>
    <div class="uppy-drag-drop-area"></div>
  </div>
</template>

<script>
import Uppy from '@uppy/core/lib/Uppy.js';
import Dashboard from '@uppy/dashboard'
import Form from '@uppy/form'
import Tus from '@uppy/tus'
import { Checksum } from '../plugins/uppy-checksum.js'
import { eventBus } from '../index.js'

import '@uppy/core/dist/style.min.css';
import '@uppy/dashboard/dist/style.min.css';

export default {
  props: {
    identifier: {
      type: String,
    },
    endpoint: {
      type: String,
    }
  },
  data: function () {
    return {
      uppy: null
    }
  },
  mounted: function () {
    this.$nextTick(function () {
      eventBus.$emit("stage-changed", "uploading")
    })

    this.uppy = new Uppy({
      autoProceed: false,
      debug: false,
    })
    this.uppy.use(Dashboard, {
      inline: true,
      width: 750,
      height: 450,
      target: '.uppy-drag-drop-area',
      hideAfterFinish: true,
      showProgressDetails: true,
      hideUploadButton: false,
      hideRetryButton: false,
      hidePauseResumeButton: false,
      hideCancelButton: false,
      proudlyDisplayPoweredByUppy: false,
      metaFields: [
        { id: 'name', name: 'Name', placeholder: 'file name' },
        { id: 'description', name: 'Description', placeholder: 'describe what the file is about' }
      ],
      locale: {}
    })
    this.uppy.use(Form, {
      target: ".uppy-dataset-metadata-form",
      getMetaFromForm: true,
      addResultToForm: false,
      triggerUploadOnSubmit: false,
      submitOnSuccess: false
    })
    this.uppy.use(Tus, { endpoint: this.endpoint })
    this.uppy.use(Checksum, { id: 'Checksum' })
    this.uppy.on('preprocess-progress', (file, data) => {
      eventBus.$emit('checksummed', data.message, file)
    })
    this.uppy.on('complete', (result) => {
      eventBus.$emit('complete', result)
    })

  },
  beforeDestroy: function () {
    this.uppy.close()
  }
}
</script>