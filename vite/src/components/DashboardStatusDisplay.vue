<script setup lang="ts">
const props = defineProps<{
  errorMessage: string | null;
  srOnlyErrorMessage: string | null;
}>();

</script>

<template>
  <div v-if="props.errorMessage" class="control-error help-block" id="logo-upload-error" aria-hidden="true">{{
    props.errorMessage }}</div>
  <div role="alert">
    <span v-if="props.srOnlyErrorMessage" class="sr-only">{{ props.srOnlyErrorMessage }}</span>
  </div>
</template>

<style scoped lang="less">
.status-display {
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