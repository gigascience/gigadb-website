<script setup lang="ts">
const props = defineProps<{
  uploadedImageLocation?: string | null;
}>();
</script>

<template>
  <div class="form-group thumbnail-container">
    <div class="control-label">Uploaded Logo</div>
    <img v-if="props.uploadedImageLocation" :src="props.uploadedImageLocation" alt="Logo" />
    <div v-else class="help-block">You did not yet upload a logo</div>
  </div>
</template>

<style scoped lang="less">
@color-darker-gray: #656565;
.thumbnail-container {
  .control-label {
    font-weight: normal;
    font-size: 13px;
    color: @color-darker-gray;
    margin-bottom: 10px;
  }
}
</style>