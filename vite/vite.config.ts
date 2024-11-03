import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'

// https://vitejs.dev/config/
export default defineConfig({
  plugins: [vue()],
  build: {
    manifest: true,
    rollupOptions: {
      input: 'src/main.ts',
    },
    modulePreload: {
      polyfill: false,
    },
    outDir: '../js/vite-logo-upload',
    emptyOutDir: true,
  },
  server: {
    port: 5173,
    origin: 'http://localhost:5173',
    strictPort: true,
    host: true
  }
})
