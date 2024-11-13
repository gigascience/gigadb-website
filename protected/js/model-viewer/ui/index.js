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
  domElements.controls = root.find(".js-controls");

  const playButton = domElements.playButtonOverlay.find(".js-play-button");
  const helpButton = domElements.controls.find(".js-controls-info-btn");
  const fullscreenButton = domElements.controls.find(".js-fullscreen-btn");
  const helpModal = root.find(".js-help-modal");
  const helpModalClose = helpModal.find(".js-help-modal-close");

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

  function handleHelp(e) {
    e.preventDefault();
    helpModal.fadeIn();
  }

  function handleHelpClose(e) {
    e.preventDefault();
    helpModal.fadeOut();
  }

  function handleFullscreen(e) {
    e.preventDefault();
    // TODO: implement fullscreen
  }

  function init() {
    domElements.loadingOverlay.hide();
    domElements.controls.hide();
    domElements.playButtonOverlay.show();
    modelState.selected = domElements.modelSelector.val() || null;
    uiView.updateUI(modelState);
    domElements.modelSelector.on("change", handleSelect);
    playButton.on("click", handlePlay);
    helpButton.on("click", handleHelp);
    fullscreenButton.on("click", handleFullscreen);
    helpModalClose.on("click", handleHelpClose);
  }

  function unmount() {
    domElements.modelSelector.off("change", handleSelect);
    playButton.off("click", handlePlay);
    helpButton.off("click", handleHelp);
    fullscreenButton.off("click", handleFullscreen);
    helpModalClose.off("click", handleHelpClose);
  }

  init();

  $(window).on("beforeunload", () => {
    unmount();
  });

  return modelState;
}
