<script setup lang="ts">
import { ref } from 'vue';

const MAX_FILE_SIZE = 1024 * 1024; // 1MB in bytes
const MAX_HEIGHT = 60; // pixels

const error = ref<string | null>(null);
const selectedFile = ref<File | null>(null);
const fileInputRef = ref<HTMLInputElement | null>(null);

const validateImage = (file: File): Promise<boolean> => {
  return new Promise((resolve, reject) => {
    // Check file size
    if (file.size > MAX_FILE_SIZE) {
      reject('File size must be less than 1MB');
      return;
    }

    // Check if it's an image
    if (!file.type.startsWith('image/')) {
      reject('File must be an image');
      return;
    }

    // Create an image object to check dimensions
    const img = new Image();
    const objectUrl = URL.createObjectURL(file);

    img.onload = () => {
      URL.revokeObjectURL(objectUrl);
      if (img.height > MAX_HEIGHT) {
        reject(`Image height must be ${MAX_HEIGHT}px or less (current height: ${img.height}px)`);
        return;
      }
      resolve(true);
    };

    img.onerror = () => {
      URL.revokeObjectURL(objectUrl);
      reject('Error loading image');
    };

    img.src = objectUrl;
  });
};

const handleFileChange = async (event: Event) => {
  const target = event.target as HTMLInputElement;
  const file = target.files?.[0];
  error.value = null;
  selectedFile.value = null;

  if (!file) return;

  try {
    await validateImage(file);
    selectedFile.value = file;
  } catch (err) {
    error.value = err as string;
    // Reset file input
    if (fileInputRef.value) {
      fileInputRef.value.value = '';
    }
  }
};
</script>

<template>
  <div class="form-group">
    <label for="Project_image_logo" class="control-label">Upload Logo</label>
    <input ref="fileInputRef" type="file" id="Project_image_logo" class="form-control" accept="image/*"
      @change="handleFileChange" name="Project[image_logo]" />
    <div role="alert">
      <div v-if="error" class="alert alert-danger mt-10">{{ error }}</div>
    </div>
    <div v-if="selectedFile" class="alert alert-gigadb-info mt-10">
      Selected file: {{ selectedFile.name }} ({{ Math.round(selectedFile.size / 1024) }} KB)
    </div>
  </div>
</template>