import { createUiState } from "./uiState.js";
import { createUiView } from "./uiView.js";
import { invariant } from "../helpers/invariant.js";

/**
 * Creates and initializes the UI component for the model viewer
 * @param {Object} param0 Configuration object
 * @param {JQuery} param0.root Root DOM element containing the model viewer UI
 * @param {function(string|null): void} param0.onSelect Callback when model is selected from dropdown
 * @param {function(string|null): void} param0.onPlay Callback when play button is clicked
 * @param {function({searchBy: string, value: string, key: string}): string|null} param0.getDataProperty Function to get file properties
 * @returns {Object} UI state object with status, error and selected model properties
 */
export function createUi({ root, onSelect, onPlay, getDataProperty }) {
  // mandatory elements
  const domElements = {
    loadingOverlay: root.find(".js-loading-overlay"),
    playButtonOverlay: root.find(".js-play-button-overlay"),
    errorDisplay: root.find(".js-error-display"),
    modelSelector: root.find(".js-model-selector"),
    modelDescription: root.find(".js-model-description"),
  };

  Object.values(domElements).forEach((el) => {
    invariant(el.length !== 0, "Expected element not found");
  });

  // optional elements
  domElements.controlsInfo = root.find(".js-controls-info")

  const uiView = createUiView(domElements, getDataProperty);

  const modelState = createUiState(
    { status: "idle", error: null, selected: null },
    () => uiView.updateUI(modelState)
  );

  function handleSelect() {
    modelState.selected = domElements.modelSelector.val() || null;
    onSelect(modelState.selected);
  }

  function handlePlay() {
    onPlay(modelState.selected);
  }

  function init() {
    domElements.loadingOverlay.hide();
    domElements.controlsInfo.hide();
    domElements.playButtonOverlay.show();
    modelState.selected = domElements.modelSelector.val() || null;
    uiView.updateUI(modelState);
    domElements.modelSelector.on("change", handleSelect);
    domElements.playButtonOverlay.find("button").on("click", handlePlay);
  }

  function unmount() {
    domElements.modelSelector.off("change", handleSelect);
    domElements.playButtonOverlay.find("button").off("click", handlePlay);
  }

  init();

  $(window).on("beforeunload", () => {
    unmount();
  });

  return modelState;
}
