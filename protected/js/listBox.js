$(document).ready(function () {
  const multiSelect = $("select[multiple]");
  multiSelect.attr("aria-multiselectable", "true");

  multiSelect.find("option").each(function () {
    const option = $(this);
    option.attr("aria-checked", option.prop("selected"));
  });

  function toggleOptionSelection(option) {
    const newState = !(option.attr("aria-checked") === "true");
    option.attr("aria-checked", newState);
    option.prop("selected", newState);
  }

  multiSelect.each(function () {
    $(this).data("focusedIndex", -1);
  });

  multiSelect.on("mousedown", "option", function (e) {
    e.preventDefault();
    const option = $(this);
    const select = option.parent();

    toggleOptionSelection(option);

    const index = select.find("option").index(option);
    select.data("focusedIndex", index);
    select.find("option").removeClass("focused");
    option.addClass("focused");

    select.trigger("change");

    select.focus();
  });

  multiSelect.on("keydown", function (e) {
    const select = $(this);
    const options = select.find("option");
    const currentFocusIndex = select.data("focusedIndex");

    const setFocusedOption = (index) => {
      options.removeClass("focused");
      options.eq(index).addClass("focused");
      select.data("focusedIndex", index);
      const optionElement = options[index];
      if (optionElement.scrollIntoView) {
        optionElement.scrollIntoView({ block: "nearest" });
      }
    };

    if (e.code === "Tab") {
      return;
    }

    // prevent default behavior for any other key
    e.preventDefault();

    switch (e.code) {
      case "Space":
      case " ":
        const focusedOption = options.eq(currentFocusIndex);

        if (focusedOption.length) {
          toggleOptionSelection(focusedOption);
          select.trigger("change");
        }
        break;

      case "ArrowDown":
        if (currentFocusIndex < options.length - 1) {
          setFocusedOption(currentFocusIndex + 1);
        }
        break;

      case "ArrowUp":
        if (currentFocusIndex > 0) {
          setFocusedOption(currentFocusIndex - 1);
        }
        break;

      case "Home":
        setFocusedOption(0);
        break;

      case "End":
        setFocusedOption(options.length - 1);
        break;
    }
  });

  multiSelect.on("focus", function (e) {
    const select = $(this);
    let focusedIndex = select.data("focusedIndex");

    if (focusedIndex === -1) {
      focusedIndex = 0;
      select.data("focusedIndex", focusedIndex);
    }

    select.find("option").eq(focusedIndex).addClass("focused");
  });

  multiSelect.on("blur", function (e) {
    $(this).find("option").removeClass("focused");
  });
});
