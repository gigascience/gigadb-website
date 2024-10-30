<script setup lang="ts">
const props = defineProps<{
  uploadedImageLocation: string | null;
  name: string;
}>();
</script>

<template>
  <input type="hidden" :name="props.name" :value="props.uploadedImageLocation" />
</template>
