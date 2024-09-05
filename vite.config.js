import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';

export default defineConfig({
    plugins: [
      laravel({
        input: ['resources/css/app.css', 'resources/js/app.js'],
        refresh: true,
      }),
    ],
    base: '/', // Asegúrate de que sea la ruta absoluta
    build: {
      outDir: 'public/build', // Ruta de salida donde se genera la build
    },
  });
  
