import { defineConfig } from "@hey-api/openapi-ts";

export default defineConfig({
  input: process.env.OPENAPI_INPUT || "http://localhost:8000/docs/api.json",
  output: process.env.OPENAPI_OUTPUT || "./resources/js/client",
  plugins: [
    "@hey-api/typescript",
    "@hey-api/client-axios",
    {
      name: "@hey-api/sdk",
      classNameBuilder: "{{name}}Service",
      classStructure: "auto",
      asClass: true,
    },
  ],
});
