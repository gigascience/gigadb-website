<script>
  $(document).ready(function () {
    $('a[href^="http"]').not('a[href*="' + window.location.host + '"]').each(function () {
      // boldly assume that if a link already has a blank attribute, it is already take care of properly
      if ($(this).attr('target') === '_blank') {
        return;
      }
      $(this).attr('target', '_blank');
      $(this).attr('rel', 'noopener noreferrer');

      // boldly assume that if a link contains a sr-only class within, the SR message is already taken care of
      if (!$(this).html().match("sr-only")) {
        $(this).append('<span class="sr-only">, opens in a new window</span>');
      }
    });
  });
</script>