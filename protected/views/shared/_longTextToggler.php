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
    id="long-text-<?php echo $id ?>"
    class="long-text-toggler"
    style="-webkit-line-clamp: <?php echo $maxLines ?>; line-clamp:<?php echo $maxLines ?>; max-height:<?php echo $height ?>;"
  >
    <?php echo $text ?>
  </div><button
    class="long-text-toggler__toggle btn btn-subtle"
    type="button"
    aria-expanded="false"
    aria-controls="long-text-<?php echo $id ?>"
    aria-label="show more"
    data-target="long-text-<?php echo $id ?>"
    style="display: none;"
  ><i class="fa fa-caret-down"></i></button>
</span>

<script>
$(document).ready(function() {
  let $text = $('#long-text-<?php echo $id ?>');
  let $btn = $('.long-text-toggler__toggle[data-target="long-text-<?php echo $id ?>"]');
  let $container = $text.closest('.long-text-toggler-container');

  function getFullTextHeight() {
    const originalStyle = $text.attr('style');
    $text.css({
        '-webkit-line-clamp': 'unset',
        'line-clamp': 'unset',
        'max-height': 'none'
    });
    const fullHeight = $text.outerHeight();
    $text.attr('style', originalStyle);

    return fullHeight;
  }

  const maxHeight = parseFloat('<?php echo $height ?>') * parseFloat($('body').css('font-size'));

  if (getFullTextHeight() > maxHeight) {
    $btn.show();

    $btn.click(function(e) {
      e.preventDefault();
      $btn = $(this);
      $text = $('#' + $btn.data('target'));
      $container = $text.closest('.long-text-toggler-container');
      const expanded = $container.hasClass('is-expanded');

      $container.toggleClass('is-expanded');
      $btn.attr('aria-expanded', !expanded)
        .attr('aria-label', expanded ? 'show more' : 'show less')
        .find('i').removeClass('fa-caret-down fa-caret-up')
        .addClass(expanded ? 'fa-caret-down' : 'fa-caret-up');
    });
  }
});
</script>