<div id="myNews" class="carousel slide span12" >
  <!-- Carousel items -->
  <div class="module-box">
    <h2>Latest news</h2>
      <div class="carousel-inner module-inner">
        <?php
        $active="active";
        $itemPerSlide=3;
        $i=0;
        foreach ($news as $key=>$temp_news){
            if($i%$itemPerSlide==0)  {?>
            <div class="<? echo $active; ?> item">
            <? }?> 
                <div class="data-block">
                  <h4><?php echo MyHtml::encode($temp_news->title); ?></h4>
                  <p>
                    <? echo '<br/>';

                    if(strlen($temp_news->body) > 100){
                      echo MyHtml::encode(substr($temp_news->body,0,100)." ...");
                    }else {
                      echo MyHtml::encode($temp_news->body);
                    }
                    ?>
                  </p>
                  <?
                  echo MyHtml::link("See More", array("news/view",'id'=>$temp_news->id));
                  ?>
                </div>
            <?php
            if($i%$itemPerSlide==($itemPerSlide-1) || $i==count($news)-1 ) {?>
                </div>
            <? }
            $i++;
            $active="";
        } ?>
      </div>
  </div>
  <!-- Carousel nav -->
  <a class="carousel-control left gigadb-arrow-button" href="#myNews" data-slide="prev">&lsaquo;</a>
  <a class="carousel-control right gigadb-arrow-button" href="#myNews" data-slide="next">&rsaquo;</a>
</div>
<script>
$('#myNews').carousel({ interval: 60000});
</script>