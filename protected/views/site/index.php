<? $this->pageTitle = Yii::app()->name ?>

<p style="text-align:center"><i>  
        GigaDB contains <? echo $count ?> discoverable, trackable, and citable datasets that have been assigned DOIs and are available for public download and use.  
        <? // echo $this->renderInternal('Yii::app()->basePath'.'/../files/html/gigadb.html?count='.$count); ?>
    </i></p>

<p>
<? $this->renderPartial('/search/_form',array('model'=>$form,'dataset'=>$dataset,'search_result'=>null)); ?>

<? if(count($news)>0) {?>
    <div id="news_slider" class="row">
        <? $this->renderPartial('news',array('news'=>$news)); ?>
    </div>
<? }?>

<div class="row">
    <div class="span8" id="dataset_slider">
        <div class="module-box">
            <h2><?=Yii::t('app' , 'Datasets and tools') ?></h2>
            <?php
            echo CHtml::dropDownList('type', "", CMap::mergeArray(array(0=>Yii::t('app' , 'All types')),MyHtml::listData($dataset_hint,'id','name')) ,array('ajax'=>array('url'=>array('site/AjaxLoadDataset'),'update'=>'#slider_partial','type'=>'POST','data'=>array('type'=>'js:$(this).val()'))));
            ?>
            <?php
            $hint="";
            foreach ($dataset_hint as $key => $value) {
              $hint=$hint.$value->name.": ".$value->description."</br>";
            }

            ?>

            <a class="hint" id="dataset-hint" data-content="<? echo $hint; ?>"></a>
            <div id="slider_partial">
            <?php $this->renderPartial('slider',array('datasets'=>$datasets)); ?>
            </div>
        </div><!--module-box-->
    </div>

    <div class="span4">
        <?=MyHtml::link(MyHtml::image("/images/rss.png")." RSS" , "/rss/latest" , array('target' => '_blank', 'class'=>'rsslink'))?>
        <div class='module-box'>



                <div class="RSS">
                  <table class="table">
                  <?php foreach($rss_arr as $item)  {?>
                          <tr>
                            <?if(get_class($item) == 'Dataset'){?>
                              <td>New dataset added on <?=$item->publication_date?>: <?=MyHtml::link("10.5524/".$item->identifier, $item->shortUrl)?> <?=$item->title?></td>
                            <?}else{?>
                              <td><?= $item->publication_date ?>: <?=$item->message?></td>
                            <?php } ?>
                          </tr>
                  <?php } ?>
                  </table>
                </div>


        </div>
    </div>
</div>

<!--script charset="utf-8" src="http://widgets.twimg.com/j/2/widget.js"></script>
<script>
new TWTR.Widget({
  version: 2,
  type: 'profile',
  rpp: 4,
  interval: 30000,
  width: 250,
  height: 300,
  theme: {
    shell: {
      background: '#333333',
      color: '#ffffff'
    },
    tweets: {
      background: '#000000',
      color: '#ffffff',
      links: '#4aed05'
    }
  },
  features: {
    scrollbar: false,
    loop: false,
    live: false,
    behavior: 'all'
  }
}).render().setUser('GigaScience').start();
</script-->

<script>
$("#dataset-hint").popover();
</script>
