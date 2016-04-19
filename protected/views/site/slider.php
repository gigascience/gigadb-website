<div id="myCarousel" class="carousel slide" >
  <!-- Carousel items -->
  <div class="carousel-inner module-inner" style='height:305px;'>
    <?php
    $active="active";
    $itemPerSlide=3;
    $i=0;
    foreach ($datasets as $key=>$dataset){
        if($i%$itemPerSlide==0)  {?>
        <div class="<? echo $active; ?> item">
        <? }?>
            <div class="data-block">
              <?php

              $url = $dataset->getImageUrl();

              echo MyHtml::link(MyHtml::image($url ,'image'), $dataset->shortUrl,array('class'=>'image-hint',  ));
              echo 'DOI: '.MyHtml::link("10.5524/".$dataset->identifier, $dataset->shortUrl);
              echo '<br/><br/>';
              $dtitle = strlen($dataset->title) > 70 ? strip_tags( substr($dataset->title , 0 , 70) ) .'...' : $dataset->title;
              echo $dtitle;
              echo '<br/><br/>';
              echo MyHtml::encode($dataset->publication_date);
              ?>
            </div>
        <?php

        if($i%$itemPerSlide==($itemPerSlide-1) || $i==count($datasets)-1 ) {?>
            </div>
        <? }
        $i++;
        $active="";
    } ?>

  </div>
  <!-- Carousel nav -->
  <a class="carousel-control left homepage-carousel-control" href="#myCarousel" data-slide="prev">&lsaquo;</a>
  <a class="carousel-control right homepage-carousel-control" href="#myCarousel" data-slide="next">&rsaquo;</a>
</div>
<script>
$('#myCarousel').carousel({ interval: 4000});
$(".image-hint").tooltip({'placement':'top'});
</script>
