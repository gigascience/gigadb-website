<?php
$html_slides = array();

foreach ($datasets as $dataset) {
  $image_url = $dataset->getImageUrl();
  $image_tag = isset($dataset->image) ? $dataset->image->tag : '';
  $date_html = '<div class="dataset-date"></div>';
  $safe_title = Yii::app()->controller->widget('CHtmlPurifier')->purify($dataset['title']);

  if (empty($image_url)) {
    $image_url = Yii::app()->baseUrl . '/images/no_image.png';
    $image_tag = '';
  }

  if (!empty($dataset['publication_date'])) {
    $date_html = sprintf('<div class="dataset-date">%s</div>', date('F j, Y', strtotime($dataset['publication_date'])));
  }

  $html_slides[] = '<div class="dataset-item">' .
            '<div class="dataset-image-wrapper">' .
              '<img class="dataset-image" src="' . CHtml::encode($image_url) . '" alt="' . CHtml::encode($image_tag) . '" loading="lazy" />' .
            '</div>' .
            '<div class="dataset-doi">' .
                '<span aria-hidden="true">DOI: </span><a href="' . CHtml::encode($dataset['shorturl']) . '" aria-label="Dataset with DOI ' . CHtml::encode($dataset['identifier']) . '">' . CHtml::encode($dataset['identifier']) . '</a>' .
            '</div>' .
            '<h3 class="dataset-title h5">' . $safe_title . '</h3>' .
            $date_html .
        '</div>';
}
?>

<div class="container">
  <div class="underline-title">
    <div>
      <h2 class="heading">Datasets and tools</h2>
    </div>
  </div>
  <?php $this->renderPartial('//shared/_carousel_slider', array(
    'slides' => $html_slides,
    'itemsPerSlide' => [
      'mobile' => 2,
      'tablet' => 3,
      'desktop' => 4
    ],
  )); ?>
</div>
