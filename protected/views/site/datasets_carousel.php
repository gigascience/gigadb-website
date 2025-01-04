<?php
$html_slides = array();
$max_title_length = 300;

foreach ($datasets as $dataset) {
  $title = $dataset['title'];
  $imageUrl = $dataset->getImageUrl();
  $date_html = '<div class="dataset-date"></div>';

  if (empty($imageUrl)) {
    $imageUrl = Yii::app()->baseUrl . '/images/no_image.png';
  }

  if (!empty($dataset['publication_date'])) {
    $date_html = sprintf('<div class="dataset-date">%s</div>', date('F j, Y', strtotime($dataset['publication_date'])));
  }

  $html_slides[] = sprintf(
    '<div class="dataset-item">
            <div class="dataset-image-wrapper">
              <img class="dataset-image" src="%s" alt="" />
            </div>
            <div class="dataset-doi">
                DOI: <a href="%s">%s</a>
            </div>
            <h3 class="dataset-title h5">%s</h3>
            %s
        </div>',
    CHtml::encode($imageUrl),
    CHtml::encode($dataset['shorturl']),
    CHtml::encode($dataset['identifier']),
    CHtml::decode($dataset['title']),
    $date_html
  );
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