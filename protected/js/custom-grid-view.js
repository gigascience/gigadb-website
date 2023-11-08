// Handle pagination
if (typeof jQuery !== "undefined") {
  function adjustPagination() {
    $(".pagination > li > a").removeClass("first-visible");
    $(".pagination > li > a").removeClass("last-visible");
    $(".pagination > li:not(.hidden)").first().children("a").addClass("first-visible");
    $(".pagination > li:not(.hidden)").last().children("a").addClass("last-visible");
  }

  adjustPagination();

  // run every time pagination triggers
  $(document).ajaxComplete(function () {
    adjustPagination();
  });
}

// Handle table sorting and filtering
$(function () {
  setupGridView();
});

function getLastFocused(grid) {
  return $(document).data(`${grid.attr('id')}-lastFocused`);
}

function setLastFocused(grid, name) {
  $(document).data(`${grid.attr('id')}-lastFocused`, name);
}

function setupGridView() {
  // setup filtering focus
  $(document).on("change", ".grid-view tr.filters input", function () {
    const grid = $(this).closest('.grid-view');

    setLastFocused(grid, this.name);
  });

  // setup sorting focus
  $(document).on("click", ".grid-view th a.sort-link", function () {
    const grid = $(this).closest('.grid-view');
    const headerId = $(this).closest('th').attr('id');

    setLastFocused(grid, headerId);
  });

  // set filter input labels
  $('.grid-view thead tr th a.sort-link').each(function () {
    const $input = $('.grid-view tr.filters input');

    if ($input.length) {
      $input.attr('aria-label', 'filter');
    }
  });
}

function handleSorting(grid) {
  if (!grid) {
    return
  }

  $(grid).find('th').attr('aria-sort', 'none');
  const sortedColumn = $(grid).find('.sort-link.asc, .sort-link.desc');

  if (sortedColumn.length) {
    const sortOrder = sortedColumn.hasClass('asc') ? 'ascending' : 'descending';
    sortedColumn.closest('th').attr('aria-sort', sortOrder);
  }
}

function handleFocus(grid) {
  if (!grid) {
    return
  }

  const lastFocused = getLastFocused(grid);

  if (lastFocused) {
    const focusedElement = $(`[name="${lastFocused}"]`, grid).length ? $(`[name="${lastFocused}"]`, grid) : $(`#${lastFocused} a.sort-link`, grid);

    if (focusedElement.length) {
      if (focusedElement.is('input[type="text"]')) {
        focusedElement.cursorEnd();
      } else {
        focusedElement.focus();
      }
    }
  }
}

function afterAjaxUpdate(id) {
  const grid = $(`#${id}`);

  handleSorting(grid);
  handleFocus(grid);
  setupGridView();
}

jQuery.fn.cursorEnd = function () {
  return this.each(function () {
    if (this.setSelectionRange) {
      this.focus();
      this.setSelectionRange(this.value.length, this.value.length);
    } else if (this.createTextRange) {
      const range = this.createTextRange();
      range.collapse(true);
      range.moveEnd('character', this.value.length);
      range.moveStart('character', this.value.length);
      range.select();
    }
    return false;
  });
};