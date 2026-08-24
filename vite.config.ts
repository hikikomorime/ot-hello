import { defineConfig } from 'vite';
import react from '@vitejs/plugin-react';
import path from 'node:path';
import { fileURLToPath } from 'node:url';

const rootDir = path.dirname(fileURLToPath(import.meta.url));

export default defineConfig(({ command }) => {
  const isServe = command === 'serve';

  return {
    plugins: [react()],
    root: isServe ? path.join(rootDir, 'admin') : rootDir,
    publicDir: isServe ? path.join(rootDir, 'admin', 'public') : false,
    server: {
      host: '0.0.0.0',
      port: 43217,
      strictPort: true,
    },
    preview: {
      host: '0.0.0.0',
      port: 43217,
      strictPort: true,
    },
    build: {
      outDir: path.join(rootDir, 'build'),
      emptyOutDir: true,
      cssCodeSplit: false,
      sourcemap: false,
      rollupOptions: {
        input: path.join(rootDir, 'admin/src/index.tsx'),
        output: {
          format: 'iife',
          name: 'OTHelloAdmin',
          entryFileNames: 'admin.js',
          assetFileNames: 'admin[extname]',
        },
      },
    },
    define: {
      'import.meta.env.VITE_PREVIEW': JSON.stringify(isServe ? '1' : '0'),
    },
  };
});
