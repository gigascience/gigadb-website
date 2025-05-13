import { selector } from "./selectors.js";

// this module determines how the UI changes when the state changes
export function createUiView(domElements, getDataProperty) {
  const {
    loadingOverlay,
    playButtonOverlay,
    errorDisplay,
    modelDescription,
    controls,
  } = domElements;

  const loadingText = loadingOverlay.find(selector.loadingText);
  const loadingDisplay = loadingOverlay.find(selector.loadingDisplay);

  function updateUI(state) {
    const { status, error, selected } = state;

    switch (status) {
      case "idle":
        loadingDisplay.hide();
        loadingText.text("");
        playButtonOverlay.show();
        controls.hide();
        break;
      case "pending":
        loadingDisplay.show();
        loadingText.text("Loading model");
        playButtonOverlay.hide();
        controls.hide();
        break;
      case "success":
        loadingDisplay.hide();
        loadingText.text("Model loaded");
        playButtonOverlay.hide();
        controls.show();
        break;
      case "error":
        loadingDisplay.hide();
        loadingText.text("");
        playButtonOverlay.show();
        controls.hide();
        break;
    }

    const errorContent = errorDisplay.find(".js-error-content");
    if (error) {
      errorDisplay.addClass(["alert", "alert-danger"]);
      errorContent.text(error);
      errorContent.show();
      errorDisplay.show();
    } else {
      errorDisplay.removeClass(["alert", "alert-danger"]);
      errorContent.text("");
      errorContent.hide();
      errorDisplay.hide();
    }

    if (selected !== null) {
      const description = getDataProperty({
        searchBy: "id",
        value: selected,
        key: "description",
      });
      modelDescription.find(".js-description-content").text(description);
    } else {
      modelDescription.find(".js-description-content").text("");
    }
  }

  return {
    updateUI,
  };
}
