<!-- https://github.com/transloadit/uppy/blob/main/examples/vue3/src/App.vue -->
<template>
  <div id="uppyDashboard">
    <h1>DEMO FROM REPO</h1>
    <h2>Inline Dashboard</h2>
    <label>
      <input
        type="checkbox"
        :checked="showInlineDashboard"
        @change="
          (event) => {
            showInlineDashboard = event.target.checked
          }
        "
      />
      Show Dashboard
    </label>
    <Dashboard
      v-if="showInlineDashboard"
      :uppy="uppy"
      :props="{
        metaFields: [{ id: 'name', name: 'Name', placeholder: 'File name' }],
      }"
    />
    <h2>Modal Dashboard</h2>
    <div>
      <button type="button" @click="open = true">Show Dashboard</button>
      <DashboardModal
        :uppy="uppy2"
        :open="open"
        :props="{
          onRequestCloseModal: handleClose,
        }"
      />
    </div>

    <h2>Drag Drop Area</h2>
    <DragDrop
      :uppy="uppy"
      :props="{
        locale: {
          strings: {
            chooseFile: 'Boop a file',
            orDragDrop: 'or yoink it here',
          },
        },
      }"
    />

    <h2>Progress Bar</h2>
    <ProgressBar
      :uppy="uppy"
      :props="{
        hideAfterFinish: false,
      }"
    />
  </div>
</template>

<script setup>
import { Dashboard, DashboardModal, DragDrop, ProgressBar } from '@uppy/vue'
</script>

<script>
import Uppy from '@uppy/core'
import { defineComponent } from 'vue'

export default defineComponent({
  computed: {
    uppy: () =>
      new Uppy({ id: 'uppy1', autoProceed: true, debug: true }),
    uppy2: () =>
      new Uppy({ id: 'uppy2', autoProceed: false, debug: true }),
  },
  data() {
    return {
      open: false,
      showInlineDashboard: false,
    }
  },
  methods: {
    handleClose() {
      this.open = false
    },
  },
})
</script>

<style src="@uppy/core/dist/style.css"></style>
<style src="@uppy/dashboard/dist/style.css"></style>
<style src="@uppy/drag-drop/dist/style.css"></style>
<style src="@uppy/progress-bar/dist/style.css"></style>

<style>
#uppyDashboard {
  font-family: Avenir, Helvetica, Arial, sans-serif;
  -webkit-font-smoothing: antialiased;
  -moz-osx-font-smoothing: grayscale;
  text-align: center;
  color: #2c3e50;
  margin-top: 60px;
}
</style>