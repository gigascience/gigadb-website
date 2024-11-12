import { createUi } from "./ui/index.js";
import { createModelViewer } from "./viewer/index.js";
import { logger } from "./helpers/logger.js";

$(document).ready(function () {
  const container = $(".js-model-view-container");
  const loadingOverlay = $(".js-loading-overlay");
  const playButtonOverlay = $(".js-play-button-overlay");
  const errorDisplay = $(".js-error-display");
  const modelSelector = $(".js-model-selector");

  const {
    init,
    loadModel,
    render,
    destroy: destroyViewer,
  } = createModelViewer(container);

  const { setup, destroy, uiState } = createUi({
    loadingOverlay,
    playButtonOverlay,
    errorDisplay,
    modelSelector,
    onSelect: handleLoadModel,
    onPlay: handleLoadModel,
  });

  async function handleLoadModel(url) {
    if (!url) {
      uiState.status = "idle";
      return;
    }

    try {
      uiState.status = "pending";
      await loadModel(url);
      uiState.status = "success";
    } catch (err) {
      logger("error", "Error loading model", err);
      uiState.error = err;
      uiState.status = "error";
    }
  }

  setTimeout(() => {
    setup();
    init();
    render();
    uiState.status = "success";
 }, 100);

  logger("info", "Model viewer initialized");

  $(window).on("beforeunload", () => {
    destroyViewer();
    destroy();
  });
});
