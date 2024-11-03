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
  app.config.errorHandler = (err, _, info) => {
    let msg = ''

    if (err instanceof Error) {
      msg = `Error: ${err.message}\nInfo: ${info}`
    } else {
      msg = `Error: ${err}\nInfo: ${info}`
    }

    import.meta.env.DEV && console.error(msg)
    entryEl.textContent = import.meta.env.PROD
      ? 'An unexpected error occurred. Please try again later.'
      : msg
  }
  app.mount(elId)
}
