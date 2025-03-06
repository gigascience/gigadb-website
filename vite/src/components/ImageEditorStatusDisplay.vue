<script setup lang="ts">
import { computed } from 'vue';
import { config } from '../config';

const { maxHeight } = config;

const props = defineProps<{
  imageDimensions: { width: number; height: number };
}>();

const isHeightExceeded = computed(() => props.imageDimensions.height > maxHeight);
const isIdealHeight = computed(() => props.imageDimensions.height === maxHeight);
const isHeightTooSmall = computed(() => props.imageDimensions.height < maxHeight);

const tooHighMessage = computed(() => isHeightExceeded.value ? `Height exceeds ${maxHeight}px limit` : null);
const idealHeightMessage = computed(() => isIdealHeight.value ? `Perfect height: ${maxHeight}px` : null);
const tooShortMessage = computed(() => isHeightTooSmall.value ? `Height (${props.imageDimensions.height}px) is smaller than ideal ${maxHeight}px` : null);

// simplify the SR only messages to avoid overwhelming users
const srOnlyErrorMessage = computed(() => isHeightExceeded.value ? `Image height exceeds ${maxHeight} pixels. Please crop further.` : null);
const srOnlySuccessMessage = computed(() => !isHeightExceeded.value ? `Image height is valid` : null);

</script>

<template>
  <div v-if="props.imageDimensions.width && props.imageDimensions.height" class="status-display">
    <span>Current dimensions: {{ props.imageDimensions.width }}px &times; {{ props.imageDimensions.height }}px</span>
    <span v-if="tooHighMessage" class="status-error" aria-hidden="true">
      <i class="fa fa-exclamation-triangle icon-subtle" aria-hidden="true"></i>
      {{ tooHighMessage }}
    </span>
    <span v-if="idealHeightMessage" class="status-success" aria-hidden="true">
      <i class="fa fa-check icon-subtle" aria-hidden="true"></i>
      {{ idealHeightMessage }}
    </span>
    <span v-if="tooShortMessage" class="status-info" aria-hidden="true">
      <i class="fa fa-info-circle icon-subtle" aria-hidden="true"></i>
      {{ tooShortMessage }}
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
  color: @color-darker-gray;
  display: flex;
  align-items: center;
  gap: 8px;
  padding: 8px 12px;
  background: @color-lighter-gray;
  border-radius: 6px;
}

.status-error {
  color: @color-brand-danger-800;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  background: @color-brand-danger-100;
  border-radius: 4px;
  border: 1px solid @color-brand-danger-400;
}

.status-success {
  color: @color-gigadb-green-900;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  background: @color-gigadb-green-50;
  border-radius: 4px;
  border: 1px solid @color-gigadb-green-400;
}

.status-info {
  color: #1e40af;
  font-weight: 500;
  display: inline-flex;
  align-items: center;
  gap: 4px;
  padding: 4px 8px;
  background: #dbeafe;
  border-radius: 4px;
  border: 1px solid #60a5fa;
}

.icon-subtle {
  opacity: 0.7;
}
</style>