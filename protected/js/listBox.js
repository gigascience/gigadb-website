$(document).ready(function () {
  console.log("listBox.js loaded");
  const multiSelect = $("select[multiple]");
  multiSelect.attr("aria-multiselectable", "true");

  // Initialize options with aria-checked
  multiSelect.find("option").each(function () {
    const option = $(this);
    option.attr("aria-checked", option.prop("selected"));
  });

  function toggleOptionSelection(option) {
    const newState = !(option.attr("aria-checked") === "true");
    option.attr("aria-checked", newState);
    option.prop("selected", newState);
  }

  // Handle keyboard interactions for the listbox
  multiSelect.each(function () {
    // Initialize with first option focused
    $(this).data("focusedIndex", 0);
    $(this).find("option").first().addClass("focused");
  });

  // Handle click on options
  multiSelect.on("mousedown", "option", function (e) {
    e.preventDefault(); // Prevent default selection behavior
    const option = $(this);
    const select = option.parent();

    // Toggle selection
    toggleOptionSelection(option);

    // Update focus
    const index = select.find("option").index(option);
    select.data("focusedIndex", index);
    select.find("option").removeClass("focused");
    option.addClass("focused");

    // Trigger change event
    select.trigger("change");

    // Maintain focus on the select element
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
      // Scroll the option into view if needed
      const optionElement = options[index];
      if (optionElement.scrollIntoView) {
        optionElement.scrollIntoView({ block: "nearest" });
      }
    };

    switch (e.code) {
      case "Space":
      case " ":
        e.preventDefault(); // Prevent page scroll
        const focusedOption = options.eq(currentFocusIndex);

        if (focusedOption.length) {
          toggleOptionSelection(focusedOption);
          select.trigger("change");
        }
        break;

      case "ArrowDown":
        e.preventDefault();
        if (currentFocusIndex < options.length - 1) {
          setFocusedOption(currentFocusIndex + 1);
        }
        break;

      case "ArrowUp":
        e.preventDefault();
        if (currentFocusIndex > 0) {
          setFocusedOption(currentFocusIndex - 1);
        }
        break;

      case "Home":
        e.preventDefault();
        setFocusedOption(0);
        break;

      case "End":
        e.preventDefault();
        setFocusedOption(options.length - 1);
        break;
    }
  });

  // Handle focus on the select element
  multiSelect.on("focus", function (e) {
    const select = $(this);
    const focusedIndex = select.data("focusedIndex") || 0;
    select.find("option").eq(focusedIndex).addClass("focused");
  });

  // Handle blur on the select element
  multiSelect.on("blur", function (e) {
    $(this).find("option").removeClass("focused");
  });
});
