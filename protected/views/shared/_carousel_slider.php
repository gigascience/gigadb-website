<?php
$root_id = 'carousel-' . uniqid();
?>

<div class="carousel-container">
  <div id="<?php echo $root_id; ?>" class="carousel slide" data-ride="carousel" data-interval="false">

    <div class="carousel-inner">
      <?php foreach ($slides as $index => $temp_slide): ?>
        <div class="carousel-item">
          <div class="carousel-block">
            <?php echo $temp_slide; ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <a class="left carousel-control" href="#<?php echo $root_id; ?>" role="button" data-slide="prev"
      title="Previous slide">
      <span class="fa fa-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous</span>
    </a>
    <a class="right carousel-control" href="#<?php echo $root_id; ?>" role="button" data-slide="next"
      title="Next slide">
      <span class="fa fa-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next</span>
    </a>

    <ol class="carousel-indicators"></ol>
  </div>
</div>

<script>

  function throttle(func, wait) {
    let timeout;
    let lastArgs;
    return function (...args) {
      lastArgs = args;
      if (!timeout) {
        func.apply(this, args);
        timeout = setTimeout(() => {
          timeout = null;
          if (lastArgs) {
            func.apply(this, lastArgs);
            lastArgs = null;
          }
        }, wait);
      }
    };
  }

  $(document).ready(function () {
    const screenSizes = [768, 992];

    function arrangeSlides() {
      const isMobile = $(window).width() < screenSizes[0];
      const isTablet = $(window).width() >= screenSizes[0] && $(window).width() < screenSizes[1];
      const chunkSize = isMobile ? 1 : isTablet ? 2 : 3;

      const $carousel = $('#<?php echo $root_id; ?>');
      const $carouselInner = $('.carousel-inner', $carousel);
      const $indicators = $('.carousel-indicators', $carousel);
      const $controls = $('.carousel-control', $carousel);
      const $items = $carouselInner.find('.carousel-item').detach();
      const totalItems = $items.length;

      $carouselInner.empty();
      $indicators.empty();

      // Hide controls and indicators if all items fit on one slide
      if (totalItems <= chunkSize) {
        $controls.hide();
        $indicators.hide();
        $carousel.removeClass('with-indicators');

        // Dynamically set max-width when fewer items than chunk size
        const maxWidth = (100 / Math.min(totalItems, chunkSize)) + '%';
        $items.css('max-width', maxWidth);
      } else {
        $controls.show();
        $indicators.show();
        $carousel.addClass('with-indicators');

        // Reset to default responsive max-widths from CSS
        $items.css('max-width', '');
      }

      for (let i = 0; i < $items.length; i += chunkSize) {
        const $slide = $('<div>').addClass('item' + (i === 0 ? ' active' : ''));
        const $row = $('<div>').addClass('row');

        $items.slice(i, i + chunkSize).each(function () {
          $row.append($(this));
        });

        $slide.append($row);
        $carouselInner.append($slide);

        $indicators.append(
          $('<li class="carousel-indicator">')
            .append(
              $('<a class="carousel-indicator-link">')
                .attr({
                  'href': '#',
                  'data-target': '#<?php echo $root_id; ?>',
                  'data-slide-to': i / chunkSize,
                  'role': 'button',
                  'aria-label': `Go to slide ${i / chunkSize + 1}`
                })
            )
            .toggleClass('active', i === 0)
        );
      }

      $('#<?php echo $root_id; ?>').carousel(0);
    }

    $('#<?php echo $root_id; ?>').carousel({
      interval: false,
      wrap: true
    });

    arrangeSlides();

    $(window).on('resize', throttle(arrangeSlides, 300));
  });
</script>