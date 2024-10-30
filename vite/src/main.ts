import { createApp } from 'vue'
import LogoUploadUppy from './components/LogoUploadUppy.vue'

const elId = '#vue-client_project-image-logo'
const entryEl = document.querySelector(elId)
if (entryEl && entryEl instanceof HTMLElement) {
  const app = createApp(LogoUploadUppy, {
    endpoint: entryEl.dataset.endpoint,
    imageLocation: entryEl.dataset.imageLocation,
    hiddenInputName: entryEl.dataset.hiddenInputName
  })
  app.mount(elId)
}
