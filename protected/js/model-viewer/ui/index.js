import { logger } from "../helpers/logger.js";
import { invariant } from "../helpers/invariant.js";

export function createUi({ root, onSelect, onPlay, getDataProperty }) {
  const loadingOverlay = root.find(".js-loading-overlay");
  const playButtonOverlay = root.find(".js-play-button-overlay");
  const errorDisplay = root.find(".js-error-display");
  const modelSelector = root.find(".js-model-selector");
  const modelDescription = root.find(".js-model-description");

  [
    loadingOverlay,
    playButtonOverlay,
    errorDisplay,
    modelSelector,
  ].forEach((el) => {
    invariant(el.length !== 0, "Expected element not found");
  });

  const playButton = playButtonOverlay.find("button");

  function showLoadingOverlay() {
    loadingOverlay.show();
  }

  function hideLoadingOverlay() {
    loadingOverlay.hide();
  }

  function showPlayButtonOverlay() {
    playButtonOverlay.show();
  }

  function hidePlayButtonOverlay() {
    playButtonOverlay.hide();
  }

  function setDescription(description) {
    modelDescription.find(".js-content").text(description);
  }

  function setError(message) {
    const errorContent = errorDisplay.find(".error-content");
    if (message) {
      errorDisplay.addClass(["alert", "alert-danger"]);
      errorContent.text(message);
      errorContent.show();
      errorDisplay.show();
    } else {
      errorDisplay.removeClass(["alert", "alert-danger"]);
      errorContent.text("");
      errorContent.hide();
      errorDisplay.hide();
    }
  }

  const createUIState = () => {
    const state = {
      status: "idle", // idle | pending | success | error
      error: null,
      selected: null, // selected file id
    };

    return new Proxy(state, {
      set: function (target, property, value) {
        logger("info", "UI state updated", { property, value });
        target[property] = value;

        if (property === "status") {
          switch (value) {
            case "idle":
              hideLoadingOverlay();
              showPlayButtonOverlay();
              break;
            case "pending":
              showLoadingOverlay();
              hidePlayButtonOverlay();
              break;
            case "success":
              hideLoadingOverlay();
              hidePlayButtonOverlay();
              break;
            case "error":
              hideLoadingOverlay();
              showPlayButtonOverlay();
              break;
          }
        }

        if (property === "error") {
          setError(value);
        }

        if (property === "selected") {
          setDescription(getDataProperty({ searchBy: "id", value: value, key: "description" }));
        }

        return true;
      },
    });
  };

  const modelState = createUIState();

  function setSelected() {
    modelState.selected = modelSelector.val() || null;
  }

  function handleSelect() {
    setSelected();
    onSelect(modelState.selected);
  }

  function handlePlay() {
    onPlay(modelState.selected);
  }

  function create() {
    loadingOverlay.hide();
    playButtonOverlay.show();
    setError(null);
    setSelected();
    modelSelector.on("change", handleSelect);
    playButton.on("click", handlePlay);
  }

  function unmount() {
    modelSelector.off("change", handleSelect);
    playButton.off("click", handlePlay);
  }

  create();

  $(window).on("beforeunload", () => {
    unmount();
  });

  return modelState;
}
