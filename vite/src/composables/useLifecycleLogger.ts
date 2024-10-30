// lifecycleLogger.js
import { onBeforeMount, onMounted, onBeforeUpdate, onUpdated, onBeforeUnmount, onUnmounted } from 'vue'

export function useLifecycleLogger(componentName: string, callback?: () => void) {
  const runEffects = callback ?? (() => {})

  console.log(`${componentName}: Component created`, runEffects())

  onBeforeMount(() => console.log(`${componentName}: onBeforeMount`, runEffects()))
  onMounted(() => console.log(`${componentName}: onMounted`, runEffects()))
  onBeforeUpdate(() => console.log(`${componentName}: onBeforeUpdate`, runEffects()))
  onUpdated(() => console.log(`${componentName}: onUpdated`, runEffects()))
  onBeforeUnmount(() => console.log(`${componentName}: onBeforeUnmount`, runEffects()))
  onUnmounted(() => console.log(`${componentName}: onUnmounted`, runEffects()))
}
