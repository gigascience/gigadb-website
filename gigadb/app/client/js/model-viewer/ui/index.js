import { createUiState } from "./uiState.js";
import { createUiView } from "./uiView.js";
import { invariant } from "../helpers/invariant.js";
import { selector } from "./selectors.js";
import { coerceSelected } from "../helpers/coerceSelected.js";

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
    viewerContainer: root.find(selector.viewerContainer),
    canvasContainer: root.find(selector.canvasContainer),
    loadingOverlay: root.find(selector.loadingOverlay),
    playButtonOverlay: root.find(selector.playButtonOverlay),
    errorDisplay: root.find(selector.errorDisplay),
    modelSelector: root.find(selector.modelSelector),
    modelDescription: root.find(selector.modelDescription),
  };

  Object.values(domElements).forEach((el) => {
    invariant(el.length !== 0, "Expected element not found");
  });

  // optional elements
  domElements.controls = root.find(selector.controls);

  const playButton = domElements.playButtonOverlay.find(selector.playButton);
  const helpButton = domElements.controls.find(selector.helpButton);
  const fullscreenButton = domElements.controls.find(selector.fullscreenButton);
  const helpModal = root.find(selector.helpModal);
  const helpModalClose = helpModal.find(selector.helpModalClose);
  const loadingDisplay = domElements.loadingOverlay.find(selector.loadingDisplay);

  const uiView = createUiView(domElements, getDataProperty);

  const modelState = createUiState(
    { status: "idle", error: null, selected: null },
    () => uiView.updateUI(modelState)
  );

  /**
   * Handles model selection from dropdown, updates state and triggers callback
   */
  function handleSelect() {
    modelState.selected = coerceSelected(domElements.modelSelector.val()) || null;
    onSelect(modelState.selected);
  }

  /**
   * Handles play button click, triggers callback with selected model
   */
  function handlePlay() {
    onPlay(modelState.selected);
  }

  /**
   * Shows help modal when help button is clicked
   * @param {Event} e Click event
   */
  function handleHelp(e) {
    e.preventDefault();
    helpModal.fadeIn();
  }

  /**
   * Hides help modal when close button is clicked
   * @param {Event} e Click event
   */
  function handleHelpClose(e) {
    e.preventDefault();
    helpModal.fadeOut();
  }

  /**
   * Toggles browser fullscreen mode
   */
  function toggleFullscreenMode() {
    document.fullscreenElement != null
      ? document.exitFullscreen()
      : document.documentElement.requestFullscreen();
  }

  /**
   * Handles fullscreen button click
   * @param {Event} e Click event
   */
  function handleFullscreen(e) {
    e.preventDefault();
    toggleFullscreenMode();
  }

  /**
   * Updates UI when fullscreen state changes
   */
  function handleFullscreenChange() {
    const isFullscreen = document.fullscreenElement != null;
    domElements.viewerContainer.toggleClass("fullscreen", isFullscreen);
  }

  /**
   * Handles keyboard shortcuts
   * @param {KeyboardEvent} e Keyboard event
   */
  function handleKeyDown(e) {
    if (e.key === "Escape" && helpModal.is(":visible")) {
      helpModal.fadeOut();
      return;
    }

    if (e.key.toLowerCase() === "h") {
      e.preventDefault();
      helpModal.is(":visible") ? helpModal.fadeOut() : helpModal.fadeIn();
    }

    if (e.key.toLowerCase() === "f") {
      e.preventDefault();
      toggleFullscreenMode();
    }
  }

  /**
   * Initializes UI state and event listeners
   */
  function init() {
    loadingDisplay.hide();
    domElements.controls.hide();
    domElements.playButtonOverlay.show();
    modelState.selected = coerceSelected(domElements.modelSelector.val()) || null;
    uiView.updateUI(modelState);
    domElements.modelSelector.on("change", handleSelect);
    playButton.on("click", handlePlay);
    helpButton.on("click", handleHelp);
    $(document).on("keydown", handleKeyDown);
    fullscreenButton.on("click", handleFullscreen);
    helpModalClose.on("click", handleHelpClose);
    $(document).on("fullscreenchange", handleFullscreenChange);
  }

  /**
   * Removes all event listeners
   */
  function unmount() {
    domElements.modelSelector.off("change", handleSelect);
    playButton.off("click", handlePlay);
    helpButton.off("click", handleHelp);
    fullscreenButton.off("click", handleFullscreen);
    helpModalClose.off("click", handleHelpClose);
    $(document).off("keydown", handleKeyDown);
    $(document).off("fullscreenchange", handleFullscreenChange);
  }

  init();

  $(window).on("beforeunload", () => {
    unmount();
  });

  return modelState;
}
