<?php
$news_slides = array();
$max_excerpt_length = 200;

foreach ($news as $temp_news) {
  // Prepare news excerpt
  $news_body = htmlspecialchars($temp_news->body);
  $news_excerpt = mb_strlen($news_body) > $max_excerpt_length
    ? mb_substr($news_body, 0, $max_excerpt_length) . '...'
    : $news_body;

  // Build news slide HTML
  $news_slides[] = sprintf(
    '<h3 class="news-title">%s</h3>
        <p class="news-body">%s</p>%s',
    htmlspecialchars($temp_news->title),
    $news_excerpt,
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
