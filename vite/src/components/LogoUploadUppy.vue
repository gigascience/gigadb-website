<script setup lang="ts">
import Uppy from '@uppy/core';
import type { UppyFile, Meta } from '@uppy/core';
import ImageEditor from '@uppy/image-editor';
import XHR from '@uppy/xhr-upload';
import DashboardPlugin from "@uppy/dashboard"
import {
  Dashboard
} from '@uppy/vue';
import { computed, reactive, ref } from 'vue';
import HiddenInput from './HiddenInput.vue';
import UploadedLogoDisplay from './UploadedLogoDisplay.vue';
import UppyDashboardWrapper from './UppyDashboardWrapper.vue';
import { config } from '../config';
import { getUppyImgDimensions } from '../utils/getUppyImgDimensions';
import StatusDisplay from './StatusDisplay.vue';

import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';
import '@uppy/image-editor/dist/style.min.css';

const { maxHeight, maxSize, maxSizeMb } = config;

const props = defineProps<{
  endpoint: string;
  imageLocation: string | null;
  hiddenInputName?: string;
}>();

const constraintsMessage = `Please upload one image file (max height ${maxHeight}px, max size ${maxSizeMb} MB)`

const isWrapperFocusable = ref(true);
const uploadedImageLocation = ref<string | null>(props.imageLocation);
const errorMessage = ref<string | null>(null);
const srOnlyErrorMessage = ref<string | null>(null);
const size = reactive({
  height: 0,
  width: 0,
});

const endpoint = computed(() => {
  return `${props.endpoint}${uploadedImageLocation.value ? `?existingLogoUrl=${uploadedImageLocation.value}` : ''}`;
})

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

uppy.use(XHR, {
  endpoint: endpoint.value,
  formData: true,
  fieldName: 'logo_image',
  withCredentials: true
});

// uppy file events
uppy.on('file-added', async (file: UppyFile<Meta, Record<string, never>>) => {
  isWrapperFocusable.value = false;
  errorMessage.value = null;

  try {
    const { height } = await getUppyImgDimensions(file);
    if (height > maxHeight) {
      errorMessage.value = `Image height (${height}px) exceeds ${maxHeight}px - please crop the image using the editor`;
      srOnlyErrorMessage.value = `Image height of ${height} pixels exceeds maximum of ${maxHeight} pixels. Please use the editor to resize.`;
      // Auto-open editor when height exceeds limit
      const dashboard = uppy.getPlugin('Dashboard') as DashboardPlugin<Meta, Record<string, never>>;
      if (dashboard) {
        dashboard.openFileEditor(file);
      }
    }
  } catch (error) {
    errorMessage.value = 'Error getting image dimensions';
  }
});

uppy.on('file-removed', () => {
  isWrapperFocusable.value = true;
});

// uppy upload events
uppy.on('upload-success', (file, response) => {
  if (response.body?.success) {
    const { image_location } = response.body;
    uploadedImageLocation.value = image_location;
  }
});

// parse response from upload server endpoint
uppy.on('upload-error', (file, error, response) => {
  if (typeof response === 'string') {
    try {
      const errorData = JSON.parse(response);
      errorMessage.value = errorData.message;
    } catch {
      errorMessage.value = 'Failed to upload file. Please try again.';
    }
  } else {
    errorMessage.value = 'Failed to upload file. Please try again.';
  }
});

uppy.on('upload-start', () => {
  errorMessage.value = '';
});

// file-editor plugin events

uppy.on('file-editor:complete', async (file: UppyFile<Meta, Record<string, never>>) => {
  try {
    const { height } = await getUppyImgDimensions(file);
    if (height > maxHeight) {
      errorMessage.value = `Image still exceeds ${maxHeight}px height - please crop further`;
      srOnlyErrorMessage.value = `Image height still exceeds ${maxHeight} pixels. Please crop further.`;
    } else {
      errorMessage.value = null;
      srOnlyErrorMessage.value = null;
    }
  } catch (error) {
    errorMessage.value = 'Error verifying image dimensions';
  }
});

</script>

<template>
  <HiddenInput v-if="hiddenInputName" :uploaded-image-location="uploadedImageLocation" :name="hiddenInputName" />
  <UploadedLogoDisplay v-if="uploadedImageLocation" :uploaded-image-location="uploadedImageLocation" />
  <UppyDashboardWrapper :is-wrapper-focusable="isWrapperFocusable">
    <template #uppy-dashboard>
      <Dashboard :uppy="uppy" :props="{
        note: constraintsMessage,
        proudlyDisplayPoweredByUppy: false,
      }" />
    </template>
  </UppyDashboardWrapper>
  <StatusDisplay :size="size" :error-message="errorMessage" :sr-only-error-message="srOnlyErrorMessage" />
</template>

<style scoped lang="less">
/* duplicating less variable here, would be better to reuse already defined color from variables.less */
@color-gigadb-green: #08893e;
@color-true-white: #ffffff;
@color-gigadb-green-800: #0d6e36;

// uppy dashboard overrides to match the site theme
:deep(.uppy-Dashboard-inner) {
  .uppy-Dashboard-browse {
    color: @color-gigadb-green;

    &:focus,
    &:hover {
      border-bottom-color: @color-gigadb-green;
    }
  }

  .uppy-Dashboard-AddFiles-info {
    display: block;
  }

  .uppy-DashboardContent-back {
    color: @color-gigadb-green;
    background: transparent;
    border: 1px @color-gigadb-green solid;

    &:focus {
      outline: solid 2px @color-gigadb-green;
      outline-offset: 2px;
    }

    &:hover {
      color: @color-true-white;
      background: @color-gigadb-green;
    }
  }

  .uppy-DashboardContent-save {
    color: @color-gigadb-green;

    &:focus {
      background: @color-gigadb-green;
      color: @color-true-white;
    }

    &:hover {
      background: @color-gigadb-green;
      color: @color-true-white;
    }
  }

  .uppy-Dashboard-Item-action {
    &:hover {
      color: @color-gigadb-green;
    }

    &:focus {
      outline: solid 2px @color-gigadb-green;
      outline-offset: 2px;
      border: none;
      box-shadow: none;
    }
  }

  .uppy-StatusBar-actionBtn {
    color: @color-true-white;
    background: @color-gigadb-green;
    border: 1px @color-gigadb-green solid;

    &:hover {
      background: @color-gigadb-green-800;
    }

    &:focus {
      color: @color-true-white;
      outline: solid 2px @color-gigadb-green;
      outline-offset: 2px;
      box-shadow: none;
    }
  }

  .uppy-ImageCropper-controls {
    padding-top: 0;
  }
}
</style>