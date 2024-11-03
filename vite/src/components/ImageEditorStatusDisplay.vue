<script setup lang="ts">
import { computed } from 'vue';
import { config } from '../config';

const { maxHeight } = config;

const props = defineProps<{
  imageDimensions: { width: number; height: number };
}>();

const isHeightExceeded = computed(() => props.imageDimensions.height > maxHeight);
const errorMessage = computed(() => isHeightExceeded.value ? `Height exceeds ${maxHeight}px limit` : null)
const srOnlyErrorMessage = computed(() => isHeightExceeded.value ? `Image height exceeds ${maxHeight} pixels. Please crop further.` : null)
const srOnlySuccessMessage = computed(() => !isHeightExceeded.value ? `Image height is valid` : null)

</script>

<template>
  <div v-if="props.imageDimensions.width && props.imageDimensions.height" class="status-display">
    <span>Current dimensions: {{ props.imageDimensions.width }}px &times; {{ props.imageDimensions.height }}px</span>
    <span v-if="errorMessage" class="status-error" aria-hidden="true">
      {{ errorMessage }}
    </span>
    <span role="status" aria-live="polite">
      <span v-if="srOnlyErrorMessage" class="sr-only">{{ srOnlyErrorMessage }}</span>
      <span v-if="srOnlySuccessMessage" class="sr-only">{{ srOnlySuccessMessage }}</span>
    </span>
  </div>
</template>

<style scoped lang="less">
.status-display {
  height: 40px;
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

.status-error {
  color: #a50f0f;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  padding: 4px 8px;
  background: #fff5f5;
  border-radius: 4px;
  border: 1px solid #feb2b2;
}
</style>