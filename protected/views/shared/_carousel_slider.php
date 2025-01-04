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

    <a class="left carousel-control" href="#<?php echo $root_id; ?>" role="button" data-slide="prev" aria-label="Previous slide">
      <span class="fa fa-chevron-left" aria-hidden="true"></span>
    </a>
    <a class="right carousel-control" href="#<?php echo $root_id; ?>" role="button" data-slide="next" aria-label="Next slide">
      <span class="fa fa-chevron-right" aria-hidden="true"></span>
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
    const options = <?php echo json_encode([
      'itemsPerSlide' => isset($itemsPerSlide) ? $itemsPerSlide : [
        'mobile' => 1,
        'tablet' => 2,
        'desktop' => 3
      ]
    ]); ?>;

    const breakpoints = {
      'tablet': 768,
      'desktop': 992
    }

    function arrangeSlides() {
      const isMobile = $(window).width() < breakpoints.tablet;
      const isTablet = $(window).width() >= breakpoints.tablet && $(window).width() < breakpoints.desktop;
      const chunkSize = isMobile ? options.itemsPerSlide.mobile
        : isTablet ? options.itemsPerSlide.tablet
          : options.itemsPerSlide.desktop;

      const $carousel = $('#<?php echo $root_id; ?>');
      const $carouselInner = $('.carousel-inner', $carousel);
      const $indicators = $('.carousel-indicators', $carousel);
      const $controls = $('.carousel-control', $carousel);
      const $items = $carouselInner.find('.carousel-item').detach();
      const totalItems = $items.length;

      $carouselInner.empty();
      $indicators.empty();

      // Set max-width based on items per slide
      const maxWidth = (100 / chunkSize) + '%';
      $items.css('max-width', maxWidth);

      // Hide controls and indicators if all items fit on one slide
      if (totalItems <= chunkSize) {
        $controls.hide();
        $indicators.hide();
        $carousel.removeClass('with-indicators');
      } else {
        $controls.show();
        $indicators.show();
        $carousel.addClass('with-indicators');
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