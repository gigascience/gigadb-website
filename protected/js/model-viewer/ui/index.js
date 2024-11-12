import { logger } from "../helpers/logger.js";

export function createUi({
  loadingOverlay,
  playButtonOverlay,
  errorDisplay,
  modelSelector,
  onSelect,
  onPlay,
}) {
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

  function setError(message) {
    if (message) {
      errorDisplay.addClass("alert", "alert-danger");
      errorDisplay.find(".error-content").text(message);
    } else {
      errorDisplay.removeClass("alert", "alert-danger");
    }
  }

  const createUIState = () => {
    const state = {
      status: 'idle', // idle | pending | success | error
      error: null,
      selected: null
    };

    return new Proxy(state, {
      set: function (target, property, value) {
        logger('info', 'UI state updated', { property, value });
        target[property] = value;

        if (property === "status") {
          switch (value) {
            case 'idle':
              hideLoadingOverlay();
              showPlayButtonOverlay();
              break;
            case 'pending':
              showLoadingOverlay();
              hidePlayButtonOverlay();
              break;
            case 'success':
              hideLoadingOverlay();
              hidePlayButtonOverlay();
              break;
            case 'error':
              hideLoadingOverlay();
              showPlayButtonOverlay();
              break;
          }
        }

        if (property === "error") {
          if (value) {
            setError(value);
          }
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
    setSelected()
    onSelect(modelState.selected);
  }

  function handlePlay() {
    onPlay(modelState.selected);
  }

  function setup() {
    loadingOverlay.hide();
    playButtonOverlay.show();
    setError(null);
    setSelected()
    modelSelector.on("change", handleSelect);
    playButton.on("click", handlePlay);
  }

  function destroy() {
    modelSelector.off("change", handleSelect);
    playButton.off("click", handlePlay);
  }

  return {
    setup,
    destroy,
    uiState: modelState,
  };
}
