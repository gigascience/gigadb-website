<div id="myNews" class="carousel slide span12" style="width:1100px" >
  <!-- Carousel items -->
 
  
  
  <div class="module-box">

      <br>
      <br>
      <br>
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
                  <h5><?php echo $temp_news->title; ?></h5>
                  <p>
                    <? 

                    if(strlen($temp_news->body) > 100){
                      echo substr($temp_news->body,0,100)." ...";
                    }else {
                      echo $temp_news->body;
                    }
                    ?>
                  </p>
                  <?
                  echo CHtml::link("See More", array("news/view",'id'=>$temp_news->id));
                  echo '<br/>';
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
  <a class="carousel-control left gigadb-arrow-button" style="padding-top: 0px !important;background: #099242"ref="#myNews" href="#myNews" data-slide="prev">&lsaquo;</a>
  <a class="carousel-control right gigadb-arrow-button" style="padding-top: 0px !important;background: #099242"ref="#myNews" href="#myNews" data-slide="next">&rsaquo;</a>

</div>
<script>
$('#myNews').carousel({ interval: 60000});
</script>