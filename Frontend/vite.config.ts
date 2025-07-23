import tailwindcss from "@tailwindcss/vite";
import react from "@vitejs/plugin-react";
import { defineConfig } from "vite";

export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    port: 3000,
    open: true,
  },
  resolve: {
    alias: {
      features: "src/features",
      assets: "src/assets",
      shared: "src/shared",
    },
  },
  optimizeDeps: {
    include: ["@heroicons/react/24/outline"],
  },
});
