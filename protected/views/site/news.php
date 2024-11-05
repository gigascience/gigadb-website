<div id="myNews" class="news-container">
  <div id="newsCarousel" class="carousel slide" data-ride="carousel" data-interval="false">

    <div class="carousel-inner">
      <?php foreach ($news as $index => $temp_news): ?>
        <div class="news-item">
          <div class="news-block">
            <h3 class="news-title"><?php echo htmlspecialchars($temp_news->title); ?></h3>
            <p class="news-body">
              <?php
              $body = htmlspecialchars($temp_news->body);
              if (mb_strlen($body) > 100) {
                echo mb_substr($body, 0, 100) . "...";
              } else {
                echo $body;
              }
              ?>
            </p>
            <?php
            echo CHtml::link("Read More", array("news/view", 'id' => $temp_news->id), array('class' => 'btn btn-link news-more-link', 'aria-label' => "Read more about {$temp_news->title}"));
            ?>
          </div>
        </div>
      <?php endforeach; ?>
    </div>

    <a class="left carousel-control" href="#newsCarousel" role="button" data-slide="prev" title="Previous news">
      <span class="fa fa-chevron-left" aria-hidden="true"></span>
      <span class="sr-only">Previous news</span>
    </a>
    <a class="right carousel-control" href="#newsCarousel" role="button" data-slide="next" title="Next news">
      <span class="fa fa-chevron-right" aria-hidden="true"></span>
      <span class="sr-only">Next news</span>
    </a>

    <ol class="carousel-indicators"></ol>
  </div>
</div>

<script>
  const screenSizes = [768, 992];
  $(document).ready(function () {
    function arrangeSlides() {
      const isMobile = $(window).width() < screenSizes[0];
      const isTablet = $(window).width() >= screenSizes[0] && $(window).width() < screenSizes[1];
      const chunkSize = isMobile ? 1 : isTablet ? 2 : 3;

      const $carousel = $('#newsCarousel');
      const $carouselInner = $('.carousel-inner');
      const $indicators = $('.carousel-indicators');
      const $controls = $('.carousel-control');
      const $items = $carouselInner.find('.news-item').detach();
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
                  'data-target': '#newsCarousel',
                  'data-slide-to': i / chunkSize,
                  'role': 'button',
                  'aria-label': `Go to slide ${i / chunkSize + 1}`
                })
            )
            .toggleClass('active', i === 0)
        );
      }

      $('#newsCarousel').carousel(0);
    }

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

    $('#newsCarousel').carousel({
      interval: false,
      wrap: true
    });

    arrangeSlides();

    $(window).on('resize', throttle(arrangeSlides, 300));
  });
</script>