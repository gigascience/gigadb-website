<?php
/**
 * Widget for toggling between short and long text descriptions
 * @param string $id Unique identifier for this toggle instance
 * @param string $description Full description text
 * @param int $maxLength Maximum length before truncating
 */
?>

<?php if (strlen($description) <= $maxLength): ?>
    <?php echo $description; ?>
<?php else: ?>
    <span class=" js-short-<?php echo $id; ?>"><?php echo substr($description, 0, $maxLength) . '...'; ?></span>
    <span class=" js-long-<?php echo $id; ?>" style="display: none;"><?php echo $description; ?></span>
    <button class="js-desc-<?php echo $id; ?> btn btn-subtle"
            data-id="<?php echo $id; ?>"
            aria-label="show more"
            aria-expanded="false"
            aria-controls="js-long-<?php echo $id; ?>">+</button>

    <script>
    $(document).ready(function() {
        $(".js-desc-<?php echo $id; ?>").click(function(e) {
            e.preventDefault();
            var id = $(this).attr('data-id');
            var isExpanded = $(this).attr('aria-expanded') === 'true';

            $(this).text(isExpanded ? '+' : '-')
              .attr('aria-label', isExpanded ? 'Show more' : 'Show less')
              .attr('aria-expanded', !isExpanded);

            $('.js-short-' + id).toggle();
            $('.js-long-' + id).toggle();
        });
    });
    </script>
<?php endif; ?>
