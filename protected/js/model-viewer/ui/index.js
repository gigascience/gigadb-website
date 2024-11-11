function loadModel() {
  // stub
}

export function createUi({
  loadingOverlay,
  playButtonOverlay,
  errorDisplay,
  modelSelector,
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
      errorDisplay.find(".error-text").text(message);
    } else {
      errorDisplay.removeClass("alert", "alert-danger");
    }
  }

  const createUIState = () => {
    const state = {
      loading: false,
      loaded: false,
      error: null,
    };

    return new Proxy(state, {
      set: function (target, property, value) {
        target[property] = value;

        if (property === "loading") {
          if (value) {
            showLoadingOverlay();
            hidePlayButtonOverlay();
          } else {
            hideLoadingOverlay();
          }
        }

        if (property === "loaded") {
          if (value) {
            hideLoadingOverlay();
            hidePlayButtonOverlay();
          } else {
            showPlayButtonOverlay();
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

  function setup() {
    loadingOverlay.hide();
    playButtonOverlay.show();
    errorDisplay.hide();
    modelSelector.on("change", loadModel);
    playButton.on("click", loadModel);
  }

  function destroy() {
    modelSelector.off("change", loadModel);
    playButton.off("click", loadModel);
  }

  return {
    setup,
    destroy,
    modelState,
  };
}