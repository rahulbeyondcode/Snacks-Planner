import tailwindcss from "@tailwindcss/vite";
import react from "@vitejs/plugin-react";
import path from "path";
import { defineConfig } from "vite";

export default defineConfig({
  plugins: [react(), tailwindcss()],
  server: {
    port: 3000,
    open: true,
  },
  resolve: {
    alias: {
      features: path.resolve(__dirname, "src/features"),
      assets: path.resolve(__dirname, "src/assets"),
      shared: path.resolve(__dirname, "src/shared"),
    },
  },
  optimizeDeps: {
    include: ["@heroicons/react/24/outline"],
  },
});
