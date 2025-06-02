<?php
/**
 * @param string $id Unique identifier
 * @param string $text Content to display
 * @param int $maxLines Number of lines to clamp
 */
?>
<span class="long-text-toggler-container">
  <div
    id="long-text-<?php echo $id ?>"
    class="long-text-toggler"
    style="--lines:<?php echo $maxLines ?>"
  >
    <?php echo $text ?>
  </div>
  <button
    class="long-text-toggler__toggle btn btn-subtle hidden"
    type="button"
    aria-expanded="false"
    aria-controls="long-text-<?php echo $id ?>"
    aria-label="show more"
    data-target="long-text-<?php echo $id ?>"
  ><i class="fa fa-caret-down"></i></button>
</span>

<script>
// defer operations until fonts are loaded, to determine correct heights
document.fonts.ready.then(function() {
  const $text = $('#long-text-<?php echo $id ?>');
  const $btn = $('.long-text-toggler__toggle[data-target="long-text-<?php echo $id ?>"]');

  const fullHeight = $text.get(0).scrollHeight;
  const clampedHeight = $text.get(0).clientHeight;

  if (fullHeight > clampedHeight) {
    $btn.removeClass('hidden');

    $btn.click(function(e) {
      const $container = $text.closest('.long-text-toggler-container');
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
