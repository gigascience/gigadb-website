<script setup lang="ts">
interface Props {
  endpoint: string;
  imageLocation: string | null;
}

const props = defineProps<Props>();

console.log('DebugDisplay props:', {
  endpoint: props.endpoint,
  imageLocation: props.imageLocation
});

</script>

<template>
  <div class="debug-display">
    <pre>{{ props }}</pre>
  </div>
</template>

<style scoped>
.debug-display {
  padding: 1rem;
  background: #f5f5f5;
  border-radius: 4px;
  font-family: monospace;
}

pre {
  margin: 0;
  white-space: pre-wrap;
  word-break: break-all;
}
</style>
