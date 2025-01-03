<?php
$html_slides = array();
$max_title_length = 150;

foreach ($slides as $slide) {
  $title = mb_strlen($slide['title']) > $max_title_length
    ? mb_substr($slide['title'], 0, $max_title_length) . '...'
    : $slide['title'];
  $html_slides[] = sprintf(
    '<div class="dataset-item">
            <div class="dataset-doi">
                DOI: <a href="%s">%s</a>
            </div>
            <h3 class="dataset-title h5">%s</h3>
            <div class="dataset-date">%s</div>
        </div>',
    CHtml::encode($slide['shorturl']),
    CHtml::encode($slide['identifier']),
    CHtml::decode($title),
    date('F j, Y', strtotime($slide['publication_date']))
  );
}
?>

<div class="container">
  <div class="underline-title">
    <div>
      <h2 class="heading">Datasets and tools</h2>
    </div>
  </div>
  <?php $this->renderPartial('//shared/_carousel_slider', array('slides' => $html_slides)); ?>
</div>