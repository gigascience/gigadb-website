<?php

/**
 * Carousel slider partial for displaying an array of slides responsively.
 *
 * @param array $slides Array of HTML strings for each slide
 * @param array $itemsPerSlide (optional) Number of items per slide for 'mobile', 'tablet', 'desktop'
 *
 * Example usage:
 * $this->renderPartial('//shared/_carousel_slider', [
 *   'slides' => $html_slides,
 *   'itemsPerSlide' => [
 *     'mobile' => 2,
 *     'tablet' => 3,
 *     'desktop' => 4
 *   ],
 * ]);
 *
 * glossary:
 *
 * - item: each repeated "unit of content" within one slide
 * - indicator: "dot" buttons to select the current slide on display
 * - control: left / right arrow buttons to cycle slides sequentially
 */

$root_id = 'carousel-' . uniqid();
?>

<div class="carousel-container">
  <div id="<?php echo $root_id; ?>" class="carousel slide" data-ride="carousel" data-interval="false">

    <div class="carousel-inner">
      <?php foreach ($slides as $index => $temp_slide) : ?>
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

<?php
Yii::app()->assetManager->forceCopy = YII_DEBUG;
$jsDir = Yii::getAlias('/gigadb/app/client/js');
$jsUrl = Yii::app()->assetManager->publish($jsDir);

Yii::app()->clientScript->registerScriptFile($jsUrl . '/throttle.js', CClientScript::POS_END, ['type' => 'module']);
?>

<script type="module">
  import { throttle } from "<?php echo $jsUrl; ?>/throttle.js";

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
    };

    function getChunkSize() {
      const width = $(window).width();
      if (width < breakpoints.tablet) return options.itemsPerSlide.mobile;
      if (width < breakpoints.desktop) return options.itemsPerSlide.tablet;
      return options.itemsPerSlide.desktop;
    }

    function setItemMaxWidth({ $items, chunkSize }) {
      const maxWidth = (100 / chunkSize) + '%';
      $items.css('max-width', maxWidth);
    }

    function updateControlsAndIndicators({ $controls, $indicators, $carousel, totalItems, chunkSize }) {
      if (totalItems <= chunkSize) {
        $controls.hide();
        $indicators.hide();
        $carousel.removeClass('with-indicators');
      } else {
        $controls.show();
        $indicators.show();
        $carousel.addClass('with-indicators');
      }
    }

    function buildSlide({ $items, startIdx, chunkSize, isActive }) {
      const $slide = $('<div>').addClass('item' + (isActive ? ' active' : ''));
      const $row = $('<div>').addClass('row');
      $items.slice(startIdx, startIdx + chunkSize).each(function () {
        $row.append($(this));
      });
      $slide.append($row);
      return $slide;
    }

    function buildIndicator({ slideIdx, isActive }) {
      return $('<li class="carousel-indicator">')
        .append(
          $('<a class="carousel-indicator-link">')
            .attr({
              'href': '#',
              'data-target': '#<?php echo $root_id; ?>',
              'data-slide-to': slideIdx,
              'role': 'button',
              'aria-label': `Go to slide ${slideIdx + 1}`
            })
        )
        .toggleClass('active', isActive);
    }

    function arrangeSlides() {
      const chunkSize = getChunkSize();
      const $carousel = $('#<?php echo $root_id; ?>');
      const $carouselInner = $('.carousel-inner', $carousel);
      const $indicators = $('.carousel-indicators', $carousel);
      const $controls = $('.carousel-control', $carousel);
      const $items = $carouselInner.find('.carousel-item').detach();
      const totalItems = $items.length;

      $carouselInner.empty();
      $indicators.empty();

      setItemMaxWidth({ $items, chunkSize });
      updateControlsAndIndicators({ $controls, $indicators, $carousel, totalItems, chunkSize });

      let slideIdx = 0;
      for (let i = 0; i < $items.length; i += chunkSize, slideIdx++) {
        const isActive = i === 0;
        $carouselInner.append(buildSlide({ $items, startIdx: i, chunkSize, isActive }));
        $indicators.append(buildIndicator({ slideIdx, isActive }));
      }

      $carousel.carousel(0);
    }

    $('#<?php echo $root_id; ?>').carousel({
      interval: false,
      wrap: true
    });

    arrangeSlides();

    $(window).on('resize', throttle(arrangeSlides, 300));
  });
</script>