<?php
$html_slides = array();
$max_title_length = 300;

foreach ($datasets as $dataset) {
  $imageUrl = $dataset->getImageUrl();
  $imageTag = $dataset->image->tag;
  $date_html = '<div class="dataset-date"></div>';

  if (empty($imageUrl)) {
    $imageUrl = Yii::app()->baseUrl . '/images/no_image.png';
    $imageTag = '';
  }

  if (!empty($dataset['publication_date'])) {
    $date_html = sprintf('<div class="dataset-date">%s</div>', date('F j, Y', strtotime($dataset['publication_date'])));
  }

  $html_slides[] = '<div class="dataset-item">' .
            '<div class="dataset-image-wrapper">' .
              '<img class="dataset-image" src="' . CHtml::encode($imageUrl) . '" alt="' . CHtml::encode($imageTag) . '" loading="lazy" />' .
            '</div>' .
            '<div class="dataset-doi">' .
                '<span aria-hidden="true">DOI: </span><a href="' . CHtml::encode($dataset['shorturl']) . '" aria-label="Dataset with DOI ' . CHtml::encode($dataset['identifier']) . '">' . CHtml::encode($dataset['identifier']) . '</a>' .
            '</div>' .
            '<h3 class="dataset-title h5">' . $dataset['title'] . '</h3>' .
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