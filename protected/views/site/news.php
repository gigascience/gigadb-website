<style>
  /* Container styles */
  .news-container {
    padding: 0;
  }

  /* Carousel layout */
  .carousel-inner>.item>.row {
    display: flex;
    flex-wrap: wrap;
    margin-block: 0;
    /* offset news block padding */
    margin-inline: -5px;
  }

  /* News item styles */
  .news-item {
    flex: 1 0 auto;
    padding: 5px;
    max-width: 100%;
  }

  .news-block {
    margin: 0;
    padding: 15px;
    border: 1px solid #ddd;
    border-radius: 4px;
    height: 200px;
    display: flex;
    flex-direction: column;
  }

  .news-block h5 {
    margin-top: 0;
    margin-bottom: 10px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
  }

  .news-block p {
    flex-grow: 1;
    margin-bottom: 10px;
    overflow: hidden;
  }

  /* Carousel controls */
  .carousel-control {
    width: 40px;
    height: 40px;
    background-color: transparent;
    color: #08893e;
    border-radius: 50%;
    top: 50%;
    transform: translateY(-50%);
    opacity: 1;
    background-image: none !important;
    display: flex;
    align-items: center;
    justify-content: center;
    text-decoration: none;
    position: absolute;
  }

  .carousel-control:hover,
  .carousel-control:focus {
    color: #0d6e36;
    text-decoration: none;
    outline: none;
  }

  .carousel-control:focus {
    outline-offset: 2px;
    outline: solid 2px #0d6e36;
  }

  /* Carousel indicators */
  .carousel-indicators {
    bottom: -30px;
    margin-bottom: 0;
  }

  .carousel-indicators li {
    border-color: #08893e;
  }

  .carousel-indicators .active {
    background-color: #08893e;
  }

  @media (min-width: 768px) {
    .news-item {
      max-width: 50%;
    }
  }

  @media (min-width: 992px) {
    .news-item {
      max-width: 33.33%;
    }

    .carousel-control.left {
      left: -50px;
    }

    .carousel-control.right {
      right: -50px;
    }
  }
</style>

<div id="myNews" class="news-container">
  <div id="newsCarousel" class="carousel slide" data-ride="carousel" data-interval="false">
    <ol class="carousel-indicators"></ol>

    <div class="carousel-inner">
      <?php foreach ($news as $index => $temp_news): ?>
        <div class="news-item">
          <div class="news-block">
            <h5><?php echo htmlspecialchars($temp_news->title); ?></h5>
            <p>
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
            echo CHtml::link("See More", array("news/view", 'id' => $temp_news->id), array('class' => 'btn btn-link'));
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
  </div>
</div>

<script>
  const screenSizes = [768, 992];
  $(document).ready(function () {
    function arrangeSlides() {
      const isMobile = $(window).width() < screenSizes[0];
      const isTablet = $(window).width() >= screenSizes[0] && $(window).width() < screenSizes[1];
      const chunkSize = isMobile ? 1 : isTablet ? 2 : 3;

      const $carouselInner = $('.carousel-inner');
      const $indicators = $('.carousel-indicators');
      const $items = $carouselInner.find('.news-item').detach();

      $carouselInner.empty();
      $indicators.empty();

      for (let i = 0; i < $items.length; i += chunkSize) {
        const $slide = $('<div>').addClass('item' + (i === 0 ? ' active' : ''));
        const $row = $('<div>').addClass('row');

        $items.slice(i, i + chunkSize).each(function () {
          $row.append($(this));
        });

        $slide.append($row);
        $carouselInner.append($slide);

        $indicators.append(
          $('<li>')
            .attr({
              'data-target': '#newsCarousel',
              'data-slide-to': i / chunkSize
            })
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