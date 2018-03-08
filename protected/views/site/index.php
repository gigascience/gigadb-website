<? $this->pageTitle = Yii::app()->name ?>
        <div class="content">
            <section class="image-background">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1 text-center">
                            <h1 class="home-search-bar-title">GIGADB DATASETS</h1>
                            <p class="home-search-bar-subtitle">GigaDB contains <? echo $count ?> discoverable, trackable, and citable datasets that have been assigned DOIs and are available for public download and use.</p>
                           
                            <? $this->renderPartial('/search/_form',array('model'=>$form,'dataset'=>$dataset,'search_result'=>null)); ?>
                                  
                        </div>
                    </div>
                </div>
            </section>
            <section style="margin-bottom: 20px;">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-8">
                            <div class="underline-title">
                                <ol class="breadcrumb pull-right">
                                    <li><a href="#">+ more</a></li>
                                </ol>
                                <div>
                                    <h4>Dataset types</h4>
                                </div>
                            </div>
                            <ul class="list-inline home-text-icon-list">
                                <li><a href="#"><div class="text-icon text-icon-green"><img src="/images/new_interface_image/Climate.svg"></div>Climate (<span><? echo $number_climate ?></span>)</a></li>
                                <li><a href="#"><div class="text-icon text-icon-blue"><img src="/images/new_interface_image/Ecology.svg"></div>Ecology (<span><? echo $number_ecology ?></span>)</a></li>
                                <li><a href="#"><div class="text-icon text-icon-yellow"><img src="/images/new_interface_image/EEG.svg"></div>EEG (<span><? echo $number_eeg ?></span>)</a></li>
                                <li><a href="#"><div class="text-icon text-icon-red"><img src="/images/new_interface_image/Epigenomic.svg"></div>Epigenomic (<span><? echo $number_epi ?></span>)</a></li>
                                <li><a href="#"><div class="text-icon text-icon-yellow"><img src="/images/new_interface_image/Genome-Mapping.svg"></div>Genome mapping (<span><? echo $number_genome_mapping ?></span>)</a></li>
                                <li><a href="#"><div class="text-icon text-icon-green"><img src="/images/new_interface_image/Genomic.svg"></div>Genomic (<span><? echo $number_genomic ?></span>)</a></li>
                                <li><a href="#"><div class="text-icon text-icon-blue"><img src="/images/new_interface_image/Imaging.svg"></div>Imaging (<span><? echo $number_imaging ?></span>)</a></li>
                                <li><a href="#"><div class="text-icon text-icon-green"><img src="/images/new_interface_image/Lipidomic.svg"></div>Lipidomic (<span><? echo $number_lipi ?></span>)</a></li>
                                <li><a href="#"><div class="text-icon text-icon-yellow"><img src="/images/new_interface_image/Metabarcoding.svg"></div>Metabarcoding (<span><? echo $number_metabarcoding ?></span>)</a></li>
                                <li><a href="#"><div class="text-icon text-icon-yellow"><img src="/images/new_interface_image/Metabolomic.svg"></div>Metabolomic (<span><? echo $number_metabolomic ?></span>)</a></li>
                                <li><a href="#"><div class="text-icon text-icon-red"><img src="/images/new_interface_image/Metadata.svg"></div>Metadata (<span><? echo $number_metadata ?></span>)</a></li>
                                <li><a href="#"><div class="text-icon text-icon-green"><img src="/images/new_interface_image/Metagenomic.svg"></div>Metagenomic (<span><? echo $number_metagenomic ?></span>)</a></li>
                            </ul>
                        </div>
                          <div class="col-xs-4">
                            <div class="underline-title">
                                <div>
                                    <h4>RSS</h4>
                                </div>
                            </div>
                                  <?php $flag=1;foreach($rss_arr as $item)  {?>
                          
                            <? if($flag>2){break;}
                            if(get_class($item) == 'Dataset'){?>
                              <p style="margin-bottom: 0px;">New dataset added on <?=$item->publication_date?>: <?=MyHtml::link("10.5524/".$item->identifier, $item->shortUrl)?> <?=$item->title?></p>
                            <?}else{?>
                              <p style="margin-bottom: 0px;"><?= $item->publication_date ?>: <?=$item->message?></p>
                            <?php } ?>
                              <hr style="border-style: dashed; border-color: #e5e5e5;">
                              <?php $flag++; } ?>
                          
                           

                        </div>
                    </div>
                </div>
            </section>
            <section>
                <div class="container">
                    <div class="color-background ">
                        <div class="row home-color-background-grid">
                            <div class="col-xs-3">
                                <div class="home-color-background-block">
                                    <div class="text-icon text-icon-o text-icon-lg"><img src="/images/new_interface_image/datasets.svg"></div>
                                    <h4><? echo $count ?></h4>
                                    <p>Datasets</p>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="home-color-background-block">
                                    <div class="text-icon text-icon-o text-icon-lg"><img src="/images/new_interface_image/samples.svg"></div>
                                    <h4><? echo $count_sample ?></h4>
                                    <p>Samples</p>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="home-color-background-block">
                                    <div class="text-icon text-icon-o text-icon-lg"><img src="/images/new_interface_image/files.svg"></div>
                                    <h4><? echo $count_file ?></h4>
                                    <p>Files</p>
                                </div>
                            </div>
                            <div class="col-xs-3">
                                <div class="home-color-background-block">
                                    <div class="text-icon text-icon-o text-icon-lg"><img src="/images/new_interface_image/volume.svg"></div>
                                    <h4>31</h4>
                                    <p>Data volume(TB)</p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </section>
        </div>

<script>
$("#dataset-hint").popover();
</script>
