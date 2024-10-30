<script setup lang="ts">
const props = defineProps<{
  uploadedImageLocation: string | null;
}>();
</script>

<template>
  <input type="hidden" name="Project[image_location]" :value="props.uploadedImageLocation" />
</template>