/**
 * Expected HTML structure as rendered by protected/views/shared/_lengthWarning.php
 */

export function initLengthWarning() {
  $(document).on('input', '[data-length-threshold]', handleInput);
}

function updateCounter({ $counter, length, limit }) {
  if ($counter && $counter.length) {
    $counter.text(`${length} / ${limit} characters.`);
  }
}

function updateWarning({ $warning, $warningMessage, isOverLimit }) {
  if (!$warning || !$warning.length || !$warningMessage || !$warningMessage.length) {
    return;
  }

  if (isOverLimit) {
    $warningMessage.show();
  } else {
    $warningMessage.hide();
  }
}

/**
 * Add or remove the warning element's ID from the input's aria-describedby list.
 */
function toggleAriaDescribedBy({ $input, warningId, show }) {
  if (!$input || !$input.length || !warningId) {
    return;
  }

  const existing = ($input.attr('aria-describedby') || '')
    .split(/\s+/)
    .filter(Boolean);

  const ids = new Set(existing);
  show ? ids.add(warningId) : ids.delete(warningId);

  $input.attr('aria-describedby', Array.from(ids).join(' '));
}

/* ───────────────────────── Event handler ───────────────────────── */

function handleInput() {
  const $input = $(this);
  const limit  = Number($input.data('length-threshold'));
  const length = $input.val().length;
  const isOverLimit   = length > limit;

  const $counter        = $(`#${$input.attr('id')}-length-count`);
  const $warning        = $(`#${$input.attr('id')}-length-warning`);
  const $warningMessage = $warning.find('.js-length-warning-message').first();

  updateCounter({ $counter, length, limit });
  updateWarning({ $warning, $warningMessage, isOverLimit });
  toggleAriaDescribedBy({ $input, warningId: $warning.attr('id'), show: isOverLimit });
}

// Auto-initialise when the module is imported.
initLengthWarning();