/// <reference types="vitest/config" />
/// <reference types="node" />
import { defineConfig } from 'vite'
import vue from '@vitejs/plugin-vue'
import {resolve} from 'path';

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
  },
  css: {
    preprocessorOptions: {
      less: {
        additionalData: `@import "${resolve(__dirname, '../less/base/variables.less')}";`,
        javascriptEnabled: true,
      },
    }
  }
})
