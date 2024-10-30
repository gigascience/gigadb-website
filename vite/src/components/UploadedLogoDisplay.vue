<script setup lang="ts">
const props = defineProps<{
  uploadedImageLocation: string;
}>();
</script>

<template>
  <div class="form-group thumbnail-container">
    <div class="control-label">Uploaded Logo</div>
    <img :src="props.uploadedImageLocation" alt="Logo" />
  </div>
</template>

<style scoped lang="less">
.thumbnail-container {
  .control-label {
    font-weight: normal;
    font-size: 13px;
    color: #656565;
    margin-bottom: 10px;
  }
}
</style>