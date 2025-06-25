import { selector } from "./selectors.js";

export const STATUS = {
  IDLE: 'idle',
  PENDING: 'pending',
  SUCCESS: 'success',
  ERROR: 'error',
};

// this module determines how the UI changes when the state changes
export function createUiView(domElements, getDataProperty) {
  const {
    loadingOverlay,
    playButtonOverlay,
    errorDisplay,
    modelDescription,
    controls,
  } = domElements;
  // vrButton directly from domElements because it is added to the DOM async


  const loadingText = loadingOverlay.find(selector.loadingText);
  const loadingDisplay = loadingOverlay.find(selector.loadingDisplay);

  function updateUI(state) {
    const { status, error, selected } = state;

    switch (status) {
      case STATUS.IDLE:
        loadingDisplay.hide();
        loadingText.text("");
        playButtonOverlay.show();
        controls.hide();
        domElements.vrButton?.hide();
        break;
      case STATUS.PENDING:
        loadingDisplay.show();
        loadingText.text("Loading model");
        playButtonOverlay.hide();
        controls.hide();
        domElements.vrButton?.hide();
        break;
      case STATUS.SUCCESS:
        loadingDisplay.hide();
        loadingText.text("Model loaded");
        playButtonOverlay.hide();
        controls.show();
        domElements.vrButton?.show();
        break;
      case STATUS.ERROR:
        loadingDisplay.hide();
        loadingText.text("");
        playButtonOverlay.show();
        controls.hide();
        domElements.vrButton?.hide();
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
