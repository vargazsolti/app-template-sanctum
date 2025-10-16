import { defineConfig } from 'vite'
import laravel from 'laravel-vite-plugin'

export default defineConfig({
  server: {
    host: 'app.test',
    port: 5174,                     // külön port a Blade asseteknek
    origin: 'http://app.test:5174', // így a Blade oldalak helyes URL-t kapnak
    cors: true,
    hmr: { host: 'app.test' },
  },
  plugins: [
    laravel({
      input: ['resources/css/app.css', 'resources/js/app.js'],
      refresh: true,
    }),
  ],
})
