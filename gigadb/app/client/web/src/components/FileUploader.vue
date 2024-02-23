<template>
  <div id="uppy">
    <form class="uppy-dataset-metadata-form">
      <input id="dataset" type="hidden" :value="identifier">
    </form>
    <div class="uppy-drag-drop-area"></div>
  </div>
</template>

<style lang="less" scoped>
@color-true-white: #ffffff;
@color-lighter-gray: #f8f8f8;
@color-light-gray: #e5e5e5;
@color-darker-gray: #656565;
@color-true-black: #000000;
@color-gigadb-green: #08893e;
@color-gigadb-green-800: #0d6e36;
@color-gigadb-green-900: #0d5a2e;

// wrapper for uppy drag n drop widget
.uppy-drag-drop-area::v-deep {
  .uppy-Dashboard-inner {
    background-color: @color-lighter-gray;
  }

  // "browse files" button
  .uppy-Dashboard-browse {
    color: @color-gigadb-green;

    &:hover {
      border-bottom: 1px solid @color-gigadb-green-900;
      color: @color-gigadb-green-900;
    }

    &:focus {
      outline: 5px auto -webkit-focus-ring-color;
    }
  }

  // "cancel", "+ add more" and "done" buttons
  .uppy-StatusBar-actionBtn,
  .uppy-DashboardContent-back,
  .uppy-DashboardContent-save,
  .uppy-DashboardContent-addMore {
    color: @color-gigadb-green;

    &:focus {
      outline: solid 2px @color-gigadb-green;
      background: transparent;
    }

    &:hover {
      background: @color-light-gray;
      color: @color-gigadb-green-800;
    }
  }

  // "upload" button
  .uppy-StatusBar.is-waiting .uppy-StatusBar-actionBtn--upload {
    background-color: @color-gigadb-green;
    color: @color-true-white;

    &:hover {
      color: @color-true-white;
      background: @color-gigadb-green-800;
    }

    &:focus {
      color: @color-true-white;
      outline-offset: 2px;
      outline: solid 2px @color-gigadb-green;
      box-shadow: none;
    }
  }

  // "completed" icon
  .uppy-Dashboard-Item-progressIcon--circle>circle {
    fill: @color-gigadb-green;
  }

  // success checkmark
  .uppy-StatusBar.is-complete .uppy-StatusBar-statusIndicator {
    color: @color-gigadb-green;
  }

  // edit button
  .uppy-Dashboard-Item-action--edit {
    color: @color-darker-gray;

    &:hover {
      color: @color-gigadb-green;
    }
  }

  // edit and delete buttons
  .uppy-size--md .uppy-Dashboard-Item-action--copyLink,
  .uppy-size--md .uppy-Dashboard-Item-action--edit,
  .uppy-Dashboard-Item-action {
    &:focus {
      box-shadow: 0 0 0 3px fade(@color-gigadb-green, 50%);
    }
  }


  // file size text
  .uppy-Dashboard-Item-status {
    color: @color-darker-gray;
  }

  // "save changes" button on file edit
  .uppy-c-btn-primary {
    color: @color-true-white;
    background: @color-gigadb-green;
    border: 1px @color-gigadb-green solid;

    &:focus {
      color: @color-true-white;
      outline-offset: 2px;
      outline: solid 2px @color-gigadb-green;
      box-shadow: none;
    }

    &:hover {
      color: @color-true-white;
      background: @color-gigadb-green-800;
    }
  }

  // "cancel" button on file edit
  .uppy-c-btn-link {
    color: @color-gigadb-green;
    background: @color-true-white;
    border: 1px @color-gigadb-green solid;

    &:focus {
      color: @color-gigadb-green;
      outline-offset: 2px;
      outline: solid 2px @color-gigadb-green;
      box-shadow: none;
    }

    &:hover {
      color: @color-true-white;
      background: @color-gigadb-green;
    }
  }
}
</style>

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