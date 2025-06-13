<?php
$news_slides = array();

foreach ($news as $temp_news) {
  // Build news slide HTML
  $news_slides[] = sprintf(
    '<h3 class="news-title">%s</h3>
    <p class="news-body">%s</p>%s',
    Yii::app()->controller->widget("CHtmlPurifier")->purify($temp_news->title),
    Yii::app()->controller->widget("CHtmlPurifier")->purify($temp_news->body),
    CHtml::link(
      "Read More",
      array("news/view", 'id' => $temp_news->id),
      array(
        'class' => 'btn btn-link news-more-link',
        'aria-label' => "Read more about {$temp_news->title}"
      )
    )
  );
}
?>

<div class="container">
  <div class="underline-title">
    <div>
      <h2 class="heading">Latest news</h2>
    </div>
  </div>
  <?php $this->renderPartial('//shared/_carousel_slider', array('slides' => $news_slides)); ?>
</div>
