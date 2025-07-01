/**
 * Expects HTML like: protected/views/shared/_lengthWarning.php
 */
export function initLengthWarning() {
  $(document).on('input', '[data-length-threshold]', function () {
    const $input   = $(this);
    const limit    = parseInt($input.data('length-threshold'), 10);
    const id       = $input.attr('id');

    const $lengthCount = $(`#${id}-length-count`);
    const $warning        = $(`#${id}-length-warning`);
    const $warningMessage = $warning.find('.js-length-warning-message').first();

    if ($lengthCount.length) {
      $lengthCount.text(`${$input.val().length} / ${limit} characters.`);
    }

    if (!$warning.length) {
      return;
    }

    if (!$warningMessage.length) {
      return;
    }

    if ($input.val().length > limit) {
      if ($warning.is(':hidden')) {
        $warning.show();
      }
      $warningMessage.show();
      toggleAriaDescribedById({
        inputId: id,
        id: $warning.attr('id'),
        show: true
      });
    } else {
      $warningMessage.hide();
      toggleAriaDescribedById({
        inputId: id,
        id: $warning.attr('id'),
        show: false
      });
    }
  });
}

function toggleAriaDescribedById({inputId, id, show}) {
  if (!inputId || !id) {
    return;
  }

  const $input = $(`#${inputId}`);

  if (!$input.length) {
    return;
  }

  const existing = ($input.attr('aria-describedby') || '')
    .split(/\s+/)
    .filter(Boolean);

  const ids = new Set(existing);

  if (show) {
    ids.add(id);
  } else {
    ids.delete(id);
  }

  const newValue = Array.from(ids).join(' ');

  $input.attr('aria-describedby', newValue);
}

initLengthWarning();