// this module determines how the UI changes when the state changes
export function createUiView(domElements, getDataProperty) {
  const {
    loadingOverlay,
    playButtonOverlay,
    errorDisplay,
    modelDescription,
    controls,
  } = domElements;

  function updateUI(state) {
    const { status, error, selected } = state;

    switch (status) {
      case "idle":
        loadingOverlay.hide();
        playButtonOverlay.show();
        controls.hide();
        break;
      case "pending":
        loadingOverlay.show();
        playButtonOverlay.hide();
        controls.hide();
        break;
      case "success":
        loadingOverlay.hide();
        playButtonOverlay.hide();
        controls.show();
        break;
      case "error":
        loadingOverlay.hide();
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
