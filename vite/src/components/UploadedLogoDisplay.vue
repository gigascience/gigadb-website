<script setup lang="ts">
const props = defineProps<{
  uploadedImageLocation?: string | null;
}>();
</script>

<template>
  <div class="form-group thumbnail-container">
    <div class="control-label">Uploaded Logo</div>
    <img v-if="props.uploadedImageLocation" :src="props.uploadedImageLocation" alt="Uploaded Logo" class="test-uploaded-project-logo" />
    <div v-else class="help-block no-logo-uploaded">You did not yet upload a logo</div>
  </div>
</template>

<style scoped lang="less">
.thumbnail-container {
  margin-bottom: 10px;
  .control-label {
    font-weight: normal;
    font-size: 13px;
    color: @color-darker-gray;
    margin-bottom: 10px;
  }
}
</style>