import React from "react";
import { createRoot } from "react-dom/client";

import App from "./App";
import "./i18n";
import { BrowserRouter } from "react-router";

const container = document.getElementById("root") as HTMLElement;
const root = createRoot(container);

root.render(
  <React.StrictMode>
    <React.Suspense fallback="loading">
    <BrowserRouter>
      <App />
      </BrowserRouter>
    </React.Suspense>
  </React.StrictMode>,
);
