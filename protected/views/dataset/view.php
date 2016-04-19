<?php
$title= strlen($model->title)>100?strip_tags(substr($model->title, 0,100))." ...":strip_tags($model->title);
$this->pageTitle="GigaDB Dataset - DOI 10.5524/".$model->identifier." - ".$title;

  $template = <<<HTML
<div>
    {items}
</div>
<div class="pull-left">
    {pager}
</div>
<div>
    {summary}
</div>
HTML;

?>

<?php $this->renderPartial('/search/_form',array('model'=>$form,
        'dataset'=>$dataset,
        'search_result'=>null,
        'previous_doi'=>$previous_doi,
        'next_doi'=>$next_doi
        )); ?>
        
<div class="row first">
    <div class="span12"><p><?= Yii::t('app' , 'Data released on')?> <?= strftime("%B %d, %Y",strtotime($model->publication_date)) ?></p></div>
</div>
<div class="row dataset-information">
    
    <!--dataset content - left slidebar-->
        <div class="span9">
                <h3 class='dataset-title'><?echo $model->title; ?></h3>
                <?php if (count($model->authors) > 0) { ?>
                <p>
                    <h4>
                    <?= $model->authorNames ?>
                    (<?=substr($model->publication_date,0,4)?>): <?= $model->title.' '.$model->publisher->name.'. '; ?>
                    <a href="http://dx.doi.org/10.5524/<?= $model->identifier; ?>">http://dx.doi.org/10.5524/<?= $model->identifier ?></a>
                    <a title="Export to Reference Manager/EndNote" href="<?= Dataset::URL_RIS . $model->identifier ?>"><span class="citation-button">RIS</span></a>
                    <a title="Export to BibTeX" href="<?= Dataset::URL_BIBTEXT . $model->identifier ?>"><span class="citation-button">BibTeX</span></a>
                    <a title="Export to Text" href="<?= Dataset::URL_TEXT . $model->identifier ?>"><span class="citation-button">Text</span></a>
                    </h4>
                </p>
                <? } ?>

                <p><?= $model->description; ?> </p>
                <span class="content-popup" <?= !Yii::app()->user->isGuest ? '' : 'data-content="Please login to contact submitter"' ?> data-original-title="">
                    <a class="btn btn-green <?= !Yii::app()->user->isGuest ? '' : 'notlogged' ?>" <?= !Yii::app()->user->isGuest ? 'href="mailto:'.$model->submitter->email.'"' : 'href="#"' ?>>
                        Contact Submitter
                    </a>
                </span>

                <div class="pull-right">
                    <p>
                        <span class="citation-popup" data-content="View citations on Google Scholar">
                            <a href="<?= $model->googleScholarLink ?>" target="_blank"><img class="dataset-des-images" src="/images/google_scholar.png"/></a>
                        </span>
                        <span class="citation-popup" data-content="View citations on Europe PubMed Central">
                            <a href="<?= $model->ePMCLink ?>" target="_blank"><img class="dataset-des-images" src="/images/ePMC.jpg"/></a>
                        </span>
                    </p>
                </div>
                <div class="clear"></div>
                
                <?php if($model->fairnuse) {
                            if( (time() < strtotime($model->fairnuse))) { ?>
                    <img src="/images/fair_use2.gif" alt="policy" style=""/>
                    <p>
                        These data are made available pre-publication under the Fort Lauderdale rules. 
                        Please respect the rights of the data producers to publish their whole dataset analysis first. 
                        The data is being made available so that the research community can make use of them for more 
                        focused studies without having to wait for publication of the whole dataset analysis paper. 
                        If you wish to perform analyses on this complete dataset, please contact the authors directly 
                        so that you can work in collaboration rather than in competition.
                    </p>
                    <p><strong>This dataset fair use agreement is in place until <?= strftime('%d %B %Y',strtotime($model->fairnuse))?></strong></p>
                <?php } } ?>

                <?php if (count($model->manuscripts) > 0) { ?>
                <h4><?= Yii::t('app' , 'Related manuscripts:')?></h4>
                <p>
                    <? foreach ($model->manuscripts as $key=>$manuscript){
                        echo 'doi:' . MyHtml::link($manuscript->identifier, $manuscript->getDOILink());
                        if ($manuscript->pmid){
                            $pubmed = MyHtml::link($manuscript->pmid , "http://www.ncbi.nlm.nih.gov/pubmed/" . $manuscript->pmid);
                            echo " (PubMed: $pubmed)";
                        }
                        echo "<br/>";
                    }
                    ?>
                </p>
                <?php } ?>

                <?php if (count($model->relations) > 0) { ?>
                <h4><?= Yii::t('app' , 'Related datasets:')?></h4>
                <p>
                    <? foreach ($model->relations as $key=>$relation){
                        echo "doi:" . MyHtml::link("10.5524/". $model->identifier, '/dataset/'.$model->identifier) ." " . $relation->relationship->name . " " .'doi:' . MyHtml::link("10.5524/".$relation->related_doi, '/dataset/'.$relation->related_doi);
                        echo "<br/>";
                    }
                    ?>
                </p>
                <?php } ?>

                <?php if (count($model->externalLinks) > 0) { ?>
                <p>
                    <?  $types = array();

                        foreach ($model->externalLinks as $key=>$externalLink){
                            $types[$externalLink->externalLinkType->name] = 1;
                        }

                        foreach ($types as $typeName => $value) {
                            $typeNameLabel = preg_replace('/(?:^|_)(.?)/e',"strtoupper('$1')",$typeName);
                            $typeNameLabel = preg_replace('/(?<=\\w)(?=[A-Z])/'," $1", $typeNameLabel);
                            $typeNameLabel = trim($typeNameLabel);

                            echo "<h4>$typeNameLabel:</h4>";
                            foreach ($model->externalLinks as $key=>$externalLink){
                                if ($externalLink->externalLinkType->name == $typeName) {
                                    echo '<p>'. MyHtml::link($externalLink->url, $externalLink->url) . '</p>';
                                }
                            }
                        }
                    ?>
                </p>

                <?php } ?>
        
                <?php if (count($model->links) > 0) { ?>

                    <?php
                    $primary_links = array();
                    $secondary_links = array();

                    foreach ($model->links as $key=>$link) {
                        if ($link->is_primary) {
                            $primary_links[] = $link;
                        }
                        if (!$link->is_primary) {
                            $secondary_links[] = $link;
                        }
                    }
                    ?>

                    <?php if (!empty($primary_links)) { ?>
                    <h4><?=Yii::t('app' , 'Accessions (data included in GigaDB):')?></h4>
                        <p>
                            <? foreach ($primary_links as $link) { ?>
                                <?
                                $tokens = explode(':', $link->link);
                                $name = $tokens[0];
                                $code = $tokens[1];
                                ?>
                                <?= $name ?>:
                                <?= MyHtml::link($code, $link->getFullUrl($link_type), array('target'=>'_blank')); ?>
                                <br/>
                            <? } ?>
                        </p>
                    <?php } ?>

                    <?php if (!empty($secondary_links)) { ?>
                        <h4><?=Yii::t('app' , 'Accessions (data not in GigaDB):')?></h4>
                        <p>
                            <?php foreach ($secondary_links as $link) { ?>
                                <?php
                                $tokens = explode(':', $link->link);
                                $name = $tokens[0];
                                $code = $tokens[1];
                                ?>
                                <?php if ($name != 'http') { ?>
                                    <?= $name ?>:
                                    <?= MyHtml::link($code, $link->getFullUrl($link_type), array('target'=>'_blank')); ?>
                                <?php }else { ?>
                                    <?= MyHtml::link($link->link , $link->link,array('target'=>'_blank')); ?>
                                <?php } ?>
                                <br/>
                            <?php } ?>
                        </p>
                    <?php } ?>

                <?php } ?>
                <?php if (count($model->projects) > 0) { ?>
                <h4><?=Yii::t('app' , 'Projects:')?></h4>
                <p>
                    <? foreach ($model->projects as $key=>$project){
                        if ($project->image_location)
                            echo "<a href='$project->url'><img src='$project->image_location' /></a>";
                        else
                            echo MyHtml::link($project->name, $project->url);

                        echo "<br/>";
                    }
                    ?>
                </p>
                <? } ?>
        </div>


        <!--dataset Image - right slidebar-->
        <div class="span3 data-img">
            <h3><? echo MyHtml::encode(implode(", ", $model->getDatasetTypes()));?></h3>
            <?php if($model->image) { 
                $url = $model->getImageUrl() ? $model->getImageUrl(): $model->image->image('image_upload');
                ?>
            <a href="<?= $url ?>" >
                <?= CHtml::image($url, $url, array('class'=>'image-hint',
                    'title'=>'<ul style="text-align:left;"><li>'.$model->image->tag.'</li><li>'.'License: '.$model->image->license.'</li><li>'.'Source: '.$model->image->source.'</li><li>'.'Photographer: '.$model->image->photographer.'</li></ul>')); ?>
            </a>
            <?php } ?>
            <br/>
            <?php if($model->datasetFunders) { ?>
            <div style="margin-top:20px;">
                <h4>Funding:</h4>
                <!--get information for Funding-->
                    <?php foreach($model->datasetFunders as $fd) { ?>
                    <ul class="funding-list">
                        <li>Funding body - <?= $fd->funder->primary_name_display ?></li>
                        <?php if($fd->funder->country) { ?><li>Location - <?= $fd->funder->country ?></li><?php } ?>
                        <?php if($fd->grant_award) { ?><li>Award ID - <?= $fd->grant_award ?></li><?php } ?>
                        <?php if($fd->comments) { ?><li>Comment - <?= $fd->comments ?></li><?php } ?>
                    </ul>
                    <?php } ?>
            </div>
            <?php } ?>
        </div>        
</div>

<div class="row">
    <div class="span12">
        <?php if($samples->getData()){?>
        <h4><?=Yii::t('app' , 'Samples:')?> <?php $this->renderPartial('_sample_setting',array('columns'=>$columns)); ?></h4>
        <?php
            $this->widget('zii.widgets.grid.CGridView', array(
                'id' => 'sample-grid',
                'dataProvider'=>$samples,
                'itemsCssClass'=>'table table-bordered',
                'template' => $template,
                'pager' => 'SiteLinkPager',
                'pagerCssClass' => '',
                'summaryText' => 'Displaying {start}-{end} of {count} Sample(s).',
                'htmlOptions' => array('style'=>'padding-top: 0px'),
                'columns' => array(
                    array(
                        'name' => 'name',
                        'type' => 'raw',
                        'value' => '$data->linkName',
                        'htmlOptions' => array('class'=>'left'),
                        'visible' => in_array('name', $columns),
                    ),
                    array(
                        'name' => 'taxonomic_id',
                        'value' => 'CHtml::link($data->species->tax_id, Species::getTaxLink($data->species->tax_id))',
                        'type' => 'raw',
                        'visible' => in_array('taxonomic_id', $columns),
                    ),
                    array(
                        'name' => 'common_name',
                        'value' => '$data->species->common_name',
                        'visible' => in_array("common_name", $columns),
                    ),
                    array(
                        'name' => 'genbank_name',
                        'value' => '$data->species->genbank_name',
                        'visible' => in_array("genbank_name", $columns),
                    ),
                    array(
                        'name' => 'scientific_name',
                        'value' => '$data->species->scientific_name',
                        'visible' => in_array("scientific_name", $columns),
                    ),
                    array(
                        'name' => 'attribute',
                        'value' => '$data->displayAttr',
                        'type' => 'raw',
                        'visible' => in_array("attribute", $columns),
                        'htmlOptions' => array('class'=>'left'),
                    ),
                ),

            ));
        ?>
        <?php } ?>
        
        <div class="clear"></div>

        <?php
            $aspera = null;
            if($model->ftp_site){
                $aspera = strstr( $model->ftp_site , 'pub/');
                if($aspera)
                    $aspera = 'http://aspera.gigadb.org/?B=' . $aspera;
            }

        ?>
        <h4><?=Yii::t('app' , 'Files:')?> <?= MyHtml::link(Yii::t('app','(FTP site)'),$model->ftp_site,array('target'=>'_blank'))?>
        <?php $this->renderPartial('_display_setting',array('setting'=>$setting));?>
        </h4>
        <?php
            $this->widget('zii.widgets.grid.CGridView', array(
                'id' => 'file-grid',
                'dataProvider'=>$files,
                'itemsCssClass'=>'table table-bordered',
                'template' => $template,
                'pager' => 'SiteLinkPager',
                'pagerCssClass' => '',
                'summaryText' => 'Displaying {start}-{end} of {count} File(s).',
                'htmlOptions' => array('style'=>'padding-top: 0px'),
                'columns' => array(
                    array(
                        'name' => 'name',
                        'type' => 'raw',
                        'value' => '$data->nameHtml',
                        'visible' => in_array('name', $setting),
                    ),
                    array(
                        'name' => 'description',
                        'value' => '$data->description',
                        'visible' => in_array('description', $setting),
                    ),
                    array(
                        'name' => 'sample_name',
                        'type' => 'raw',
                        'value' => '$data->sampleName',
                        'visible' => in_array('sample_id', $setting),
                    ),
                    array(
                        'name' => 'type_id',
                        'value' => '$data->type->name',
                        'visible' => in_array("type_id", $setting),
                    ),
                    array(
                        'name' => 'format_id',
                        'value' => '$data->format->name',
                        'visible' => in_array("format_id", $setting),
                    ),
                    array(
                        'name' => 'size',
                        'value' => 'File::staticBytesToSize($data->size)',
                        'visible' => in_array("size", $setting),
                    ),
                    array(
                        'name' => 'date_stamp',
                        'value' => '$data->date_stamp',
                        'visible' => in_array("date_stamp", $setting),
                    ),
                    array(
                        'name' => 'attribute',
                        'type' => 'raw',
                        'value' => '$data->attrDesc',
                        'visible' => in_array("attribute", $setting),
                    ),
                    array(
                        'class'=>'CButtonColumn',
                        'template' => '{download}',
                        'buttons' => array(
                            'download' => array(
                                'label'=>'',
                                'url' => '$data->location',
                                'imageUrl' => '',
                                'options' => array(
                                    'target' => '_blank',
                                    'class' => 'download-btn js-download-count',
                                ),
                            )
                        ),
                        'visible' => in_array("location", $setting),
                    ),

                ),

            ));
        ?>
    </div>
</div>

<!-- HISTORY LOG  -->
<?php if ($model->isPublic && $logs) : ?>
    <div class="dataset_log">
        <h4><?= Yii::t('app' , 'History:')?></h4>
        <a id="js-expand-btn" class="btn btn-expand"><div class="history-status"> + </div></a>
        <a id="js-close-btn" class="btn btn-collapse" style='display:none;'><div class="history-status"> - </div></a>
        <div class="js-logs" style='display:none;'>
            <table class="table table-bordered">
                <thead><tr><th class="span3">Date</th><th class="span8">Action</th></tr></thead>
                <tbody>
                    <?php foreach($logs as $log) : ?>
                        <tr>
                            <td><?= date('F j, Y', strtotime($log->created_at)) ?></td>
                            <td><?= $log->message ?></td>
                        </tr>
                    <?php endforeach ?>
                </tbody>
            </table>
        </div>
    </div>
<?php endif ?>
<!-- /HISTORY LOG  -->

<div class="clear"></div>
<div class="row">
    <div class="pull-right">
        <div class="count-btn" id="facebook-share-btn">
            <script>(function(d){
                var js, id = 'facebook-jssdk'; if (d.getElementById(id)) {return;}
                    js = d.createElement('script'); js.id = id; js.async = true;
                js.src = "//connect.facebook.net/en_US/all.js#xfbml=1";
                d.getElementsByTagName('head')[0].appendChild(js);
                }(document));
            </script>
          <fb:share-button href="<?=Yii::app()->createUrl('dataset/'.$model->identifier)?>" type="button_count">
          </fb:share-button>
        </div>
        <div class="count-btn">
            <div class="g-plus" data-action="share" data-annotation="bubble"></div>
        </div>
        <div class="count-btn">
            <a href="https://twitter.com/share" class="twitter-share-button" data-via="GigaScience">Tweet</a>
            <script>!function(d,s,id){var js,fjs=d.getElementsByTagName(s)[0];if(!d.getElementById(id)){js=d.createElement(s);js.id=id;js.src="//platform.twitter.com/widgets.js";fjs.parentNode.insertBefore(js,fjs);}}(document,"script","twitter-wjs");
            </script>
        </div>
    </div>

</div>

<?php if (count($relates)) : ?>

<div class="container">
<h4><?= Yii::t('app' , 'Other datasets you might like:')?></h4>
  <div class="span10 offset1 content-carousel">
    
    <div class="well">
     
        <div id="myCarousel" class="carousel slide giga-carousel" data-total="<?php echo count($relates) ?>">
         
            <ol class="carousel-indicators hide-indicators">
                <li data-target="#myCarousel" data-slide-to="0" class="active"></li>
                
                <?php if (count($relates) > 3) : ?>
                    <li data-target="#myCarousel" data-slide-to="1"></li>
                <?php endif ?>
                
                <?php if (count($relates) > 6) : ?>
                    <li data-target="#myCarousel" data-slide-to="2"></li>
                <?php endif ?>
            </ol>
             
            <!-- Carousel items -->
            <div class="carousel-inner giga-carousel-inner">
                
                <?php foreach (array_chunk($relates, 3) as $key => $relateByTree) : ?>
                
                    <div class="item <?= $key == 0 ? 'active' : ''?>" >
                        <div class="row-fluid">
                            
                            <?php foreach ($relateByTree as $relate) : ?>
                            
                                <?php $title = strip_tags($relate->title) ?>
                                <?php $url = $relate->getImageUrl() ? $relate->getImageUrl() : $relate->image->image('image_upload') ?>
                                <div class="span4">
                                    <a href="<?= $relate->shortUrl ?>" class="thumbnail">
                                        <img src="<?= $url ? $url : 'http://placehold.it/250x250'?>" alt="Image">                
                                    </a>
                                    <a class="link-doi" href="http://dx.doi.org/10.5524/<?= $relate->identifier; ?>">
                                        DOI: 10.5524/<?= $relate->identifier ?>
                                    </a>
                                    <p><?= CHtml::encode(strlen($title) > 50 ? substr($title, 0,50)."...": $title)?></p>
                                    <p><?= strftime('%Y-%m-%d', strtotime($relate->publication_date))?></p>
                                </div>
                            
                            <?php endforeach ?>
                            
                        </div>
                    </div>
                
                <?php endforeach ?>
             
            </div><!--/carousel-inner-->
         
            <?php if (count($relates) > 3) : ?>
                <a class="carousel-control left homepage-carousel-control gigadb-arrow-button" href="#myCarousel" data-slide="prev">‹</a>
                <a class="carousel-control right homepage-carousel-control gigadb-arrow-button" href="#myCarousel" data-slide="next">›</a>
            <?php endif ?>
                
        </div><!--/myCarousel-->
     
    </div><!--/well-->
  </div>
</div>

<?php endif ?>

<!-- Place this tag in your head or just before your close body tag. -->
<script>

/* Document ready for Thumbnail Slider */
/* ----------------------------------- */
$(document).ready(function() {    
    // If the related are more than 3 so we add the caroussel
    if ($('#myCarousel').attr('data-total') > 3) {
        $('#myCarousel').carousel({
            interval: 4000,
            wrap: 'circular'
        });
    }
});
/* ----------------------------------- */

$(".hint").tooltip({'placement':'right'});
$(".image-hint").tooltip({'placement':'top'});

$("#js-expand-btn").click(function(){
      $(this).hide();
      $("#js-close-btn").show();
      $(".js-logs").show();
});

$("#js-close-btn").click(function(){
      $(this).hide();
      $("#js-expand-btn").show();
      $(".js-logs").hide();
});

$(".js-download-count").click(function(){
    var location = $(this).attr('href');
    $.ajax({
       type: 'POST',
       url: '/adminFile/downloadCount',
       data:{'file_href': location},
       success: function(response){
            if(response.success) {
            } else {
                alert(response.message);
            }
          },
      error:function(){
        }   
    });
});

$(".content-popup").popover({'placement':'right'});
$(".citation-popup").popover({'placement':'top'});
</script>
<script type="text/javascript" src="https://apis.google.com/js/plusone.js"></script>

<script type="text/javascript">
    $(".js-desc").click(function(e) {
        e.preventDefault();
        id = $(this).attr('data');
        $(this).hide();
        $('.js-short-'+id).toggle();
        $('.js-long-'+id).toggle();
    });
</script>
