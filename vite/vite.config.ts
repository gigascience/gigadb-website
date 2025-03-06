/// <reference types="vitest/config" />
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
    emptyOutDir: true,
  },
  server: {
    port: 5173,
    origin: 'http://localhost:5173',
    strictPort: true,
    host: true,
    cors: {
      origin: 'http://gigadb.gigasciencejournal.com',
      methods: ['GET', 'OPTIONS']
    }
  },
  test: {
    globals: true,
    environment: 'happy-dom'
  }
})
