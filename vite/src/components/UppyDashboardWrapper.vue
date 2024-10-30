<script setup lang="ts">
import { ref } from 'vue';
import { config } from '../config';

const { maxHeight, maxSizeMb } = config;

const props = defineProps<{
  isWrapperFocusable: boolean;
}>();

const srOnlyConstraintsMessage = `Please upload one image file. Max height ${maxHeight} pixels, max size ${maxSizeMb} megabyte${maxSizeMb === 1 ? '' : 's'}. Press Enter key to browse your local files, or drag and drop an image into this box.`

function triggerUppyButton() {
  const uppyDashboard = document.querySelector('.uppy-Dashboard-inner');
  if (uppyDashboard) {
    const triggerButton = uppyDashboard.querySelector('.uppy-Dashboard-browse') as HTMLButtonElement;
    triggerButton?.click();
  }
}
</script>

<template>
  <button :tabindex="props.isWrapperFocusable ? 0 : -1" type="button" class="uppy-dashboard-wrapper"
  :aria-label="`Upload Logo. ${srOnlyConstraintsMessage}`" @keydown.enter="triggerUppyButton">
    <slot name="uppy-dashboard"></slot>
  </button>
</template>

<style scoped>
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
</style>