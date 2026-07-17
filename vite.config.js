import { defineConfig } from "vite";
import laravel from "laravel-vite-plugin";
import tailwindcss from "@tailwindcss/vite";

export default defineConfig({
  plugins: [
    laravel({
      input: ["resources/css/app.css", "resources/js/app.js"],
      refresh: true,
    }),
    tailwindcss(),
  ],
  server: {
    host: "0.0.0.0",
    port: 5173,
    // Browser connects from host, so HMR points back to localhost
    hmr: { host: "localhost" },
    watch: { usePolling: true }, // ponytail: polling needed for bind-mounts on some hosts
  },
});
