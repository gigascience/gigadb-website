<script setup lang="ts">
import { reactive, ref } from 'vue';
import Uppy from '@uppy/core';
import type { UppyFile, Meta } from '@uppy/core';
import {
  Dashboard
} from '@uppy/vue';
import ImageEditor from '@uppy/image-editor';

import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';
import '@uppy/image-editor/dist/style.min.css';

/**

flow

- user drops or loads file in widget
- is file height > 60px?
  - yes: auto open editor
  - no: do nothing
- user resizes image in editor and saves
  - dynamically show image dimensions
  - while image height is > 60px, show warning message and keep "save" button disabled
  - show "autocrop" button. On press: --> this feature is way too complicated for what it's worth
    - if image height > 60px, auto resize height to 60px keeping image proportions
    - if image height <= 60px, do nothing
- user clicks upload button -> server endpoint uploads file to S3 and returns url

- widget props
  - server endpoint url
 */

// constants

// image constraints
const maxHeight = 60; // in pixels
const maxSize = 1000000; // in bytes
const maxSizeMb = maxSize / 1e6;

const constraintsMessage = `Please upload one image file (max height ${maxHeight}px, max size ${maxSizeMb} MB)`
const srOnlyConstraintsMessage = `Please upload one image file. Max height ${maxHeight} pixels, max size ${maxSizeMb} megabyte${maxSizeMb === 1 ? '' : 's'}. Press Enter key to browse your local files, or drag and drop an image into this box.`

// state

const isWrapperFocusable = ref(true);
const size = reactive({
  height: 0,
  width: 0,
});
const errorMessage = ref<string | null>(null);
const srOnlyErrorMessage = ref<string | null>(null);

// uppy config

const uppy = new Uppy({
  autoProceed: false,
  debug: true,
  restrictions: {
    maxNumberOfFiles: 1,
    allowedFileTypes: ['image/*'],
    maxFileSize: maxSize,
  }
})

uppy.use(ImageEditor, {
  quality: 0.8,
  cropperOptions: {
    viewMode: 1,
    background: false,
    autoCropArea: 1,
    responsive: true,
    // Show current dimensions in the cropper
    crop(event) {
      size.width = Math.round(event.detail.width);
      size.height = Math.round(event.detail.height);
    }
  },
  actions: {
    revert: true,
    rotate: false,
    granularRotate: false,
    flip: false,
    zoomIn: true,
    zoomOut: true,
    cropSquare: false,
    cropWidescreen: false,
    cropWidescreenVertical: false,
  }
});

function openFileEditor(file: UppyFile<Meta, Record<string, never>>) {
  const dashboard = uppy.getPlugin('Dashboard');
  if (dashboard) {
    // TODO fix typescript error, avoid using any
    (dashboard as any).openFileEditor(file);
  }
}

async function getImgDimension(imgFile: UppyFile<Meta, Record<string, never>>): Promise<{ width: number; height: number }> {
  return new Promise((resolve, reject) => {
    const url = URL.createObjectURL(imgFile.data);
    const img = new Image();
    img.onload = () => {
      URL.revokeObjectURL(img.src);
      resolve({ width: img.width, height: img.height });
    };
    img.onerror = (error) => {
      URL.revokeObjectURL(img.src);
      reject(error);
    };
    img.src = url;
  });
}

// file events

uppy.on('file-added', async (file: UppyFile<Meta, Record<string, never>>) => {
  console.log('file-added', file);

  isWrapperFocusable.value = false;
  errorMessage.value = null;

  try {
    const { height } = await getImgDimension(file);
    if (height > maxHeight) {
      // Show warning but don't remove file
      errorMessage.value = `Image height (${height}px) exceeds ${maxHeight}px - please resize using the editor`;
      srOnlyErrorMessage.value = `Image height of ${height} pixels exceeds maximum of ${maxHeight} pixels. Please use the editor to resize.`;
      // Auto-open editor when height exceeds limit
      openFileEditor(file);
    }
  } catch (error) {
    errorMessage.value = 'Error getting image dimensions';
  }
});

uppy.on('file-removed', () => {
  isWrapperFocusable.value = true;
});

// file-editor events

uppy.on('file-editor:complete', async (file: UppyFile<Meta, Record<string, never>>) => {
  try {
    const { height } = await getImgDimension(file);
    if (height > maxHeight) {
      errorMessage.value = `Image still exceeds ${maxHeight}px height - please resize further`;
      srOnlyErrorMessage.value = `Image height still exceeds ${maxHeight} pixels. Please resize further.`;
    } else {
      errorMessage.value = null;
      srOnlyErrorMessage.value = null;
    }
  } catch (error) {
    errorMessage.value = 'Error verifying image dimensions';
  }
});

// methods

function triggerUppyButton() {
  const uppyDashboard = document.querySelector('.uppy-Dashboard-inner');
  if (uppyDashboard) {
    const triggerButton = uppyDashboard.querySelector('.uppy-Dashboard-browse') as HTMLButtonElement;
    triggerButton?.click();
  }
}
</script>

<template>
  <div :class="`form-group ${errorMessage ? 'has-error' : ''}`">
    <label class="control-label" for="logo-upload">Image Logo</label>
    <!-- <span id="logo-upload-description" class="control-description help-block">Please upload a logo image file.</span> -->
    <!-- <span id="logo-upload-constraints" class="control-description help-block">{{ constraintsMessage }}</span> -->
    <button :tabindex="isWrapperFocusable ? 0 : -1" type="button" class="uppy-dashboard-wrapper"
      :aria-label="`Upload Logo. ${srOnlyConstraintsMessage}`" @keydown.enter="triggerUppyButton">
      <Dashboard :uppy="uppy" :props="{
        note: constraintsMessage,
        proudlyDisplayPoweredByUppy: false,
      }" />
    </button>
    <div v-if="size.width && size.height" class="dimensions-display">
      <span>Current dimensions: {{ size.width }}px &times; {{ size.height }}px</span>
      <span role="status" aria-live="polite">
        <span v-if="size.height > maxHeight" class="dimension-warning">
          (Height exceeds {{ maxHeight }}px limit)
        </span>
      </span>
    </div>
    <div role="alert">
      <div v-if="errorMessage" class="control-error help-block" id="logo-upload-error" aria-hidden="true">{{
        errorMessage }}</div>
      <span v-if="srOnlyErrorMessage" class="sr-only">{{ srOnlyErrorMessage }}</span>
    </div>
  </div>
</template>

<style scoped lang="less">
.uppy-dashboard-wrapper {
  width: 100%;
  background: none;
  border: none;
  padding: 0;
  margin: 0;
  font: inherit;
  color: inherit;
  cursor: pointer;
  border-radius: 4px;
  border: 1px solid transparent;
  cursor: default;
}

.uppy-dashboard-wrapper:focus {
  outline: none;
}

.uppy-dashboard-wrapper:focus-visible {
  outline: 1px solid #08893e;
  border: 1px solid #06b34d;
  box-shadow: inset 0 1px 1px rgba(8, 137, 62, 0.075), 0 0 6px rgba(6, 179, 77, 0.5);
  border-radius: 4px;
}

:deep(.uppy-Dashboard-inner) {

  /* overrides */
  .uppy-Dashboard-browse {
    /* duplicating less variable here, would be better to reuse already defined color from variables.less */
    color: #08893e;

    &:focus,
    &:hover {
      border-bottom-color: #08893e;
    }
  }

  .uppy-Dashboard-AddFiles-info {
    display: block;
  }

  .uppy-DashboardContent-back {
    color: #08893e;
    background: transparent;
    border: 1px #08893e solid;

    &:focus {
      outline: solid 2px #08893e;
      outline-offset: 2px;
    }

    &:hover {
      color: #fff;
      background: #08893e;
    }
  }

  .uppy-DashboardContent-save {
    color: #08893e;
    &:focus {
      background: #08893e;
      color: #fff;
    }
    &:hover {
      background: #08893e;
      color: #fff;
    }
  }

  .uppy-Dashboard-Item-action {

    // color: white;
    // background: black;
    // border: 1px #08893e solid;
    &:hover {
      color: #08893e;
      // background: #08893e;
    }

    &:focus {
      outline: solid 2px #08893e;
      outline-offset: 2px;
      border: none;
      box-shadow: none;
    }
  }

  .uppy-StatusBar-actionBtn {
    color: #fff;
    background: #08893e;
    border: 1px #08893e solid;

    &:hover {
      // color: #fff;
      background: #0d6e36;
    }

    &:focus {
      color: #fff;
      outline: solid 2px #08893e;
      outline-offset: 2px;
      box-shadow: none;
    }
  }

  .uppy-ImageCropper-controls {
    padding-top: 0;
  }
}

.dimensions-display {
  margin-top: 12px;
  font-size: 0.875rem;
  color: #4a5568;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background: #f7fafc;
  border-radius: 6px;
}

.dimension-warning {
  color: #e53e3e;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  padding: 4px 8px;
  background: #fff5f5;
  border-radius: 4px;
  border: 1px solid #feb2b2;
}
</style>
<!-- Ref logo w h = 138px 58px -->
<!-- height should be fixed, width should be variable and maintain aspect ratio -->
