<script setup lang="ts">
import { ref, computed, onMounted } from 'vue';
import Uppy from '@uppy/core';
import {
  Dashboard
} from '@uppy/vue';
import type { UppyFile, Meta } from '@uppy/core';

import '@uppy/core/dist/style.css';
import '@uppy/dashboard/dist/style.css';

const uppy = new Uppy({
  autoProceed: false,
  debug: true,
  restrictions: {
    maxNumberOfFiles: 1,
    allowedFileTypes: ['image/*'],
    maxFileSize: 1000000,
  }
})

const errorMessage = ref<string | null>(null);

uppy.on('file-added', async (file: UppyFile<Meta, Record<string, never>>) => {
  console.log('file-added', file);
  try {
    const { height } = await getImgDimension(file);
    if (height > 60) {
      uppy.removeFile(file.id);
      errorMessage.value = 'Image height exceeds 60px';
    } else {
      errorMessage.value = null;
    }
  } catch (error) {
    console.error('Error getting image dimensions', error);
    errorMessage.value = 'Error getting image dimensions';
  }
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

const ariaDescribedBy = computed(() => {
  return errorMessage.value ? 'logo-upload-description logo-upload-error' : 'logo-upload-description';
});
</script>

<template>
  <div class="form-group">
    <label class="control-label" for="logo-upload">Upload Logo</label>
    <span id="logo-upload-description" class="control-description help-block">Please upload a logo image file.</span>
    <Dashboard :uppy="uppy" :props="{
      note: 'Images only, 1 file, maximum height of 60px, up to 1 MB',
      proudlyDisplayPoweredByUppy: false,
    }" :aria-describedby="ariaDescribedBy" />
    <div role="alert">
      <span v-if="errorMessage" class="error-message" id="logo-upload-error">{{ errorMessage }}</span>
    </div>
  </div>
</template>

<style scoped>
@import '../style.css';
</style>
<!-- Ref logo w h = 138px 58px -->
<!-- height should be fixed, width should be variable and maintain aspect ratio -->
