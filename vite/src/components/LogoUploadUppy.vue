<script setup lang="ts">
import { ref } from 'vue';
import Uppy from '@uppy/core';
import {
  Dashboard
} from '@uppy/vue';
import type { UppyFile, Meta } from '@uppy/core';
import Form from '@uppy/form';
import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';

// image constraints
const maxHeight = 60;
const maxSize = 1000000;
const maxSizeMb = maxSize / 1e6;

const isWrapperFocusable = ref(true);
const selectedFile = ref<UppyFile<Meta, Record<string, never>> | null>(null);
const fileData = ref<string | null>(null);

const uppy = new Uppy({
  autoProceed: false,
  debug: true,
  restrictions: {
    maxNumberOfFiles: 1,
    allowedFileTypes: ['image/*'],
    maxFileSize: maxSize,
  }
})

uppy.use(Form, {
  target: '#project-form',
  resultName: 'Project[image_logo]',
  addResultToForm: true
});

uppy.on('complete', (result) => {
  console.log('Upload complete:', result);
});

const constraintsMessage = `Please upload one image file (max height ${maxHeight}px, max size ${maxSizeMb} MB)`
const srOnlyConstraintsMessage = `Please upload one image file. Max height ${maxHeight} pixels, max size ${maxSizeMb} megabyte${maxSizeMb === 1 ? '' : 's'}. Press Enter key to browse your local files, or drag and drop an image into this box.`
const errorMessage = ref<string | null>(null);
const srOnlyErrorMessage = ref<string | null>(null);

const handleFileSelection = (file: UppyFile<Meta, Record<string, never>>) => {
  // selectedFile.value = file;
  // const fileInfo = {
  //   id: file.id,
  //   name: file.name,
  //   type: file.type,
  //   size: file.size,
  //   meta: file.meta
  // };


  // fileData.value = JSON.stringify(fileInfo);
  // You might want to trigger any necessary form updates here
}

uppy.on('file-added', async (file: UppyFile<Meta, Record<string, never>>) => {
  console.log('file-added', file);

  try {
    const { height } = await getImgDimension(file);
    if (height > 60) {
      uppy.removeFile(file.id);
      srOnlyErrorMessage.value = 'Image height exceeds 60 pixels';
      errorMessage.value = 'Image height exceeds 60px';
    } else {
      isWrapperFocusable.value = false;
      errorMessage.value = null;
      handleFileSelection(file);
    }
  } catch (error) {
    errorMessage.value = 'Error getting image dimensions';
  }
});

uppy.on('file-removed', () => {
  isWrapperFocusable.value = true;
});

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
        hideUploadButton: true,
        height: '300px',
      }" />
    </button>
    <div role="alert">
      <div v-if="errorMessage" class="control-error help-block" id="logo-upload-error" aria-hidden="true">{{ errorMessage }}</div>
      <span v-if="srOnlyErrorMessage" class="sr-only">{{ srOnlyErrorMessage }}</span>
    </div>
  </div>
  <!-- <div v-if="selectedFile"> -->
    <!-- <p>Selected file</p> -->
    <!-- <pre>{{ JSON.stringify(selectedFile, null, 2) }}</pre> -->
    <!-- Use a hidden input to store the file information and send it to the controller on form submission -->
    <!-- <input type="hidden" :value="fileData ?? ''" name="Project[image_logo]" id="Project_image_logo" /> -->
    <!-- Display file name for visual representation -->
    <!-- <div>Selected file: {{ selectedFile.name }}</div> -->
  <!-- </div> -->
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
}
</style>
<!-- Ref logo w h = 138px 58px -->
<!-- height should be fixed, width should be variable and maintain aspect ratio -->
