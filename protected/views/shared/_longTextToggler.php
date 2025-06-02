<?php
/**
 * @param string $id Unique identifier
 * @param string $text Content to display
 * @param int $maxLines Number of lines to clamp
 */
$height = 1.4 * $maxLines . 'em'; // 1.4 is the line-height
?>
<span class="long-text-toggler-container">
  <div
    id="long-text-<?= $id ?>"
    class="long-text-toggler"
    style="-webkit-line-clamp: <?= $maxLines ?>; line-clamp: <?= $maxLines ?>; max-height: <?= $height ?>;"
  >
    <?= $text ?>
  </div><button
    class="long-text-toggler__toggle btn btn-subtle"
    type="button"
    aria-expanded="false"
    aria-controls="long-text-<?= $id ?>"
    aria-label="show more"
    data-target="long-text-<?= $id ?>"
    style="display: none;"
  ><i class="fa fa-caret-down"></i></button>
</span>

<script>
$(document).ready(function() {
  // Check if the text actually needs truncation
  var $text = $('#long-text-<?= $id ?>');
  var $btn = $('.long-text-toggler__toggle[data-target="long-text-<?= $id ?>"]');
  var $container = $text.closest('.long-text-toggler-container');

  // Temporarily remove line-clamp to measure full height
  var originalStyle = $text.attr('style');
  $text.css({
    '-webkit-line-clamp': 'unset',
    'line-clamp': 'unset',
    'max-height': 'none'
  });

  var fullHeight = $text.outerHeight();
  var maxHeight = parseFloat('<?= $height ?>') * parseFloat($('body').css('font-size'));

  // Restore original styling
  $text.attr('style', originalStyle);

  // Only show button if content exceeds max height
  if (fullHeight > maxHeight) {
    $btn.show();

    // Set up click handler
    $btn.click(function(e) {
      e.preventDefault();
      var $btn = $(this);
      var $text = $('#' + $btn.data('target'));
      var $container = $text.closest('.long-text-toggler-container');
      var expanded = $container.hasClass('is-expanded');

      $container.toggleClass('is-expanded');
      $btn.attr('aria-expanded', !expanded)
          .attr('aria-label', expanded ? 'show more' : 'show less')
          .find('i').removeClass('fa-caret-down fa-caret-up')
          .addClass(expanded ? 'fa-caret-down' : 'fa-caret-up');
    });
  }
});
</script>