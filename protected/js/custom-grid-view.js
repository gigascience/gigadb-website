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

let tabbed = 0 // 1 = tab forward, -1 = tab backward, 0 = no tab

function getLastFocused(grid) {
  return $(document).data(`${grid.attr('id')}-lastFocused`);
}

function setLastFocused(grid, name) {
  $(document).data(`${grid.attr('id')}-lastFocused`, name);
}

function getTabbableSibling(el, prev) {
  const tabbableEls = $( ":tabbable" ) // requires jQuery UI

  const currentIndex = tabbableEls.index(el);

  const siblingIndex = prev ? currentIndex - 1 : currentIndex + 1;

  if (siblingIndex >= 0 && siblingIndex < tabbableEls.length) {
    return tabbableEls.eq(siblingIndex);
  }

  return prev ? tabbableEls.last() : tabbableEls.first();
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

  // handle tab
  $(document).on("keydown", ".grid-view tr.filters input", function (event) {
    if (event.key === 'Tab') {
      if (event.shiftKey) {
        tabbed = -1
      } else {
        tabbed = 1
      }
    }
  });

  $(document).on("keyup", ".grid-view tr.filters input", function (event) {
    if (event.key === 'Tab') {
      tabbed = 0
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
    let focusedElement = $(`[name="${lastFocused}"]`, grid).length ? $(`[name="${lastFocused}"]`, grid) : $(`#${lastFocused} a.sort-link`, grid);

    if (tabbed === 1) {
      focusedElement = getTabbableSibling(focusedElement, false);
    } else if (tabbed === -1) {
      focusedElement = getTabbableSibling(focusedElement, true);
    }

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