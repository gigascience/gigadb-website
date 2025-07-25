<script setup lang="ts">
import { computed, reactive, ref } from 'vue';

import Uppy from '@uppy/core';
import type { UppyFile, Meta } from '@uppy/core';

import ImageEditor from '@uppy/image-editor';
import XHR from '@uppy/xhr-upload';
import DashboardPlugin from "@uppy/dashboard"
import { Dashboard } from '@uppy/vue';

import HiddenInput from './HiddenInput.vue';
import DashboardStatusDisplay from './DashboardStatusDisplay.vue';
import ImageEditorStatusDisplay from './ImageEditorStatusDisplay.vue';
import UploadedLogoDisplay from './UploadedLogoDisplay.vue';

import { config } from '../config';
import { getUppyImgDimensions } from '../utils/getUppyImgDimensions';

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
const srOnlyConstraintsMessage = `Please upload one image file. Maximum height ${maxHeight} pixels, maximum size ${maxSizeMb} megabyte${maxSizeMb === 1 ? '' : 's'}. Press Enter key to browse your local files, or drag and drop an image into this box.`

function triggerUppyButton() {
  const uppyDashboard = document.querySelector('.uppy-Dashboard-inner');
  if (uppyDashboard) {
    const triggerButton = uppyDashboard.querySelector('.uppy-Dashboard-browse') as HTMLButtonElement;
    triggerButton?.click();
  }
}

const isWrapperFocusable = ref(true);
const isEditing = ref(false);
const uploadedImageLocation = ref<string | null>(props.imageLocation);
const errorMessage = ref<string | null>(null);
const srOnlyErrorMessage = ref<string | null>(null);
const imageDimensions = reactive({
  height: 0,
  width: 0,
});

const endpoint = computed(() => {
  return `${props.endpoint}${uploadedImageLocation.value ? `?existingLogoUrl=${uploadedImageLocation.value}` : ''}`;
})

const uppy = new Uppy({
  autoProceed: false,
  debug: false,
  restrictions: {
    maxNumberOfFiles: 1,
    allowedFileTypes: ['image/*'],
    maxFileSize: maxSize,
  },
  onBeforeUpload: () => {
    if (imageDimensions.height > maxHeight) {
      uppy.log(
        errorMessage.value ?? ''
      );
      uppy.info(errorMessage.value ?? '', 'error');
      return false;
    }
    return true;
  },
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
      imageDimensions.width = Math.round(event.detail.width);
      imageDimensions.height = Math.round(event.detail.height);
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

function openFileEditor(file: UppyFile<Meta, Record<string, never>>) {
  const dashboard = uppy.getPlugin('Dashboard') as DashboardPlugin<Meta, Record<string, never>>;
  if (dashboard) {
    dashboard.openFileEditor(file);
  }
}

function canUploadImage(file: UppyFile<Meta, Record<string, never>>) {
  return imageDimensions.height <= maxHeight && file.size != null && file.size <= maxSize;
}

async function handleImageHeightMsgs(file: UppyFile<Meta, Record<string, never>>, cbTooTall?: () => void) {
  const { height, width } = await getUppyImgDimensions(file);

  imageDimensions.width = width;
  imageDimensions.height = height;

  if (height > maxHeight) {
    errorMessage.value = `Image height (${height}px) exceeds ${maxHeight}px - please crop the image using the editor`;
    srOnlyErrorMessage.value = `Image height of ${height} pixels exceeds maximum of ${maxHeight} pixels. Please crop the image using the editor.`;
    cbTooTall?.();
  } else {
    errorMessage.value = null;
    srOnlyErrorMessage.value = null;
  }
}

function resetUploader() {
  uppy.cancelAll();
  uploadedImageLocation.value = null;
  errorMessage.value = null;
  srOnlyErrorMessage.value = null;
  isWrapperFocusable.value = true;
  isEditing.value = false;
  imageDimensions.height = 0;
  imageDimensions.width = 0;
}

// uppy file events
uppy.on('file-added', async (file: UppyFile<Meta, Record<string, never>>) => {
  isWrapperFocusable.value = false;
  errorMessage.value = null;

  try {
    await handleImageHeightMsgs(file, () => openFileEditor(file));

    if (canUploadImage(file)) {
      uppy.upload();
    }
  } catch (error) {
    errorMessage.value = 'Error getting image dimensions';
  }
});

uppy.on('file-removed', () => {
  isWrapperFocusable.value = true;
});

// uppy upload events
uppy.on('upload-start', () => {
  errorMessage.value = '';
});

uppy.on('upload-success', (_, response) => {
  if (response.body?.success) {
    const { image_location } = response.body;
    uploadedImageLocation.value = image_location;
  }
});

// parse response from upload server endpoint
uppy.on('upload-error', (_, __, response) => {
  const defaultError = 'Failed to upload file. Please try again.';

  if (!response) {
    errorMessage.value = defaultError;
    return;
  }

  if (typeof response === 'string') {
    try {
      const parsedResponse = JSON.parse(response);
      errorMessage.value = parsedResponse?.message || defaultError;
    } catch {
      errorMessage.value = defaultError;
    }
    return;
  }

  errorMessage.value = defaultError;
});

// file-editor plugin events
uppy.on('file-editor:start', () => {
  isEditing.value = true;
});
uppy.on('file-editor:cancel', async () => {
  isEditing.value = false;
});

uppy.on('file-editor:complete', async (file: UppyFile<Meta, Record<string, never>>) => {
  try {
    await handleImageHeightMsgs(file);

    if (canUploadImage(file)) {
      uppy.upload();
    }
  } catch (error) {
    errorMessage.value = 'Error verifying image dimensions';
  } finally {
    isEditing.value = false;
  }
});
</script>

<template>
  <div class="logo-upload-uppy">
    <HiddenInput v-if="hiddenInputName" :uploaded-image-location="uploadedImageLocation" :name="hiddenInputName" />
    <UploadedLogoDisplay :uploaded-image-location="uploadedImageLocation" />
    <button type="button" class="btn background-btn-o mb-20" v-show="!!uploadedImageLocation"
      @click="resetUploader">Upload a different logo</button>
    <button :tabindex="isWrapperFocusable ? 0 : -1" type="button" class="uppy-dashboard-wrapper"
      :aria-label="`Upload Logo. ${srOnlyConstraintsMessage}`" @keydown.enter="triggerUppyButton">
      <Dashboard :uppy="uppy" :props="{
        note: constraintsMessage,
        proudlyDisplayPoweredByUppy: false,
        hideProgressAfterFinish: true,
      }" />
    </button>
    <ImageEditorStatusDisplay v-if="isEditing" :image-dimensions="imageDimensions" />
    <DashboardStatusDisplay :error-message="errorMessage" :sr-only-error-message="srOnlyErrorMessage" />
  </div>
</template>

<style scoped lang="less">
.logo-upload-uppy {
  margin-bottom: 20px;
}

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
  outline: 1px solid @color-gigadb-green;
  border: 1px solid @color-gigadb-green-600;
  box-shadow: inset 0 1px 1px fade(@color-gigadb-green, 7.5%), 0 0 6px fade(@color-gigadb-green-600, 50%);
  border-radius: 4px;
}

// uppy dashboard overrides to match the site theme
:deep(.uppy-Dashboard-inner) {
  font-family: "Open Sans", Lato, "PT Sans", Arial, "Microsoft Yahei", "Hiragino Sans GB", "WenQuanYi Zen Hei Mono", sans-serif;

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
    color: @gigadb-green-on-lighter-gray;
    background: transparent;
    border: 1px @color-gigadb-green solid;

    &:focus {
      outline: solid 2px @color-gigadb-green;
      outline-offset: 2px;
    }

    &:hover {
      color: @color-true-white;
      background: @gigadb-green-on-lighter-gray;
    }
  }

  .uppy-DashboardContent-save {
    color: @gigadb-green-on-lighter-gray;

    &:focus {
      background: @gigadb-green-on-lighter-gray;
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