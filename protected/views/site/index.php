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
                                <ol class="breadcrumb pull-right" style="cursor:pointer">
                                    <li><span>+ more</span></li>
                                </ol>
                                <div>
                                    <h4>Dataset types</h4>
                                </div>
                            </div>
                            <ul class="list-inline home-text-icon-list">
                                <li><a href="/search/new?keyword=Genomic&dataset_type%5B%5D=2"><div class="text-icon text-icon-green"><img src="/images/new_interface_image/Genomic.svg"></div>Genomic (<span><? echo $number_genomic ?></span>)</a></li>
                                <li><a href="/search/new?keyword=Software&dataset_type%5B%5D=6"><div class="text-icon text-icon-blue"><img src="/images/new_interface_image/Software.svg"></div>Software (<span><? echo $number_software ?></span>)</a></li>
                                <li><a href="/search/new?keyword=Transcriptomic&dataset_type%5B%5D=4"><div class="text-icon text-icon-blue"><img src="/images/new_interface_image/Transcriptomic.svg"></div>Transcriptomic (<span><? echo $number_ts ?></span>)</a></li>
                                <li><a href="/search/new?keyword=Imaging&dataset_type%5B%5D=7"><div class="text-icon text-icon-blue"><img src="/images/new_interface_image/Imaging.svg"></div>Imaging (<span><? echo $number_imaging ?></span>)</a></li>                                                               
                                <li><a href="/search/new?keyword=Neuroscience&dataset_type%5B%5D=11"><div class="text-icon text-icon-yellow"><img src="/images/new_interface_image/Neuroscience.svg"></div>Neuroscience (<span><? echo $number_ns ?></span>)</a></li>  
                                <li><a href="/search/new?keyword=Epigenomic&dataset_type%5B%5D=1"><div class="text-icon text-icon-red"><img src="/images/new_interface_image/Epigenomic.svg"></div>Epigenomic (<span><? echo $number_epi ?></span>)</a></li>
                                <li><a href="/search/new?keyword=Metagenomic&dataset_type%5B%5D=3"><div class="text-icon text-icon-green"><img src="/images/new_interface_image/Metagenomic.svg"></div>Metagenomic (<span><? echo $number_metagenomic ?></span>)</a></li>
                                <li><a href="/search/new?keyword=Genome-Mapping&dataset_type%5B%5D=13"><div class="text-icon text-icon-yellow"><img src="/images/new_interface_image/Genome-Mapping.svg"></div>Genome mapping (<span><? echo $number_genome_mapping ?></span>)</a></li>
                                <li><a href="/search/new?keyword=Workflow&dataset_type%5B%5D=5"><div class="text-icon text-icon-red"><img src="/images/new_interface_image/Workflow.svg"></div>Workflow (<span><? echo $number_wf ?></span>)</a></li>
                                <li><a href="/search/new?keyword=Proteomic&dataset_type%5B%5D=10"><div class="text-icon text-icon-yellow"><img src="/images/new_interface_image/Proteomic.svg"></div>Proteomic (<span><? echo $number_proteomic ?></span>)</a></li>
                                <li><a href="/search/new?keyword=Metabarcoding&dataset_type%5B%5D=17"><div class="text-icon text-icon-yellow"><img src="/images/new_interface_image/Metabarcoding.svg"></div>Metabarcoding (<span><? echo $number_metabarcoding ?></span>)</a></li>
                                <li><a href="/search/new?keyword=Metadata&dataset_type%5B%5D=16"><div class="text-icon text-icon-red"><img src="/images/new_interface_image/Metadata.svg"></div>Metadata (<span><? echo $number_metadata ?></span>)</a></li>  

                            </ul>
                            <ul class="list-inline home-text-icon-list" style="display: none;">
                                 <li><a href="/search/new?keyword=climate&dataset_type%5B%5D=18"><div class="text-icon text-icon-green"><img src="/images/new_interface_image/Climate.svg"></div>Climate (<span><? echo $number_climate ?></span>)</a></li>   
                                 <li><a href="/search/new?keyword=Network-Analysis&dataset_type%5B%5D=12"><div class="text-icon text-icon-blue"><img src="/images/new_interface_image/Network-Analysis.svg"></div>Network-Analysis (<span><? echo $number_na ?></span>)</a></li>
                                 <li><a href="/search/new?keyword=ElectroEncephaloGraphy(EEG)&dataset_type%5B%5D=15"><div class="text-icon text-icon-yellow"><img src="/images/new_interface_image/EEG.svg"></div>EEG (<span><? echo $number_eeg ?></span>)</a></li>                        
                                 <li><a href="/search/new?keyword=Phenotyping&dataset_type%5B%5D=21"><div class="text-icon text-icon-red"><img src="/images/new_interface_image/Phenotyping.svg"></div>Phenotyping (<span><? echo $number_pt ?></span>)</a></li>
                                 <li><a href="/search/new?keyword=Metabolomic&dataset_type%5B%5D=8"><div class="text-icon text-icon-yellow"><img src="/images/new_interface_image/Metabolomic.svg"></div>Metabolomic (<span><? echo $number_metabolomic ?></span>)</a></li>
                                 <li><a href="/search/new?keyword=Lipidomic&dataset_type%5B%5D=20"><div class="text-icon text-icon-green"><img src="/images/new_interface_image/Lipidomic.svg"></div>Lipidomic (<span><? echo $number_lipi ?></span>)</a></li>                                
                                 <li><a href="/search/new?keyword=ecology&dataset_type%5B%5D=19"><div class="text-icon text-icon-blue"><img src="/images/new_interface_image/Ecology.svg"></div>Ecology (<span><? echo $number_ecology ?></span>)</a></li>
                                 <li><a href="/search/new?keyword=Virtual-Machine&dataset_type%5B%5D=14"><div class="text-icon text-icon-green"><img src="/images/new_interface_image/Virtual-Machine.svg"></div>Virtual-Machine (<span><? echo $number_vm ?></span>)</a></li>                                 
                                
                            </ul>
                        </div>
                          <div class="col-xs-4" id="rss" style="height:300px;overflow:scroll;">
                            <div class="underline-title">
                                <div>
                                    <h4>RSS</h4>
                                </div>
                            </div>
                                  <?php $flag=1;foreach($rss_arr as $item)  {?>
                          
                            <? if($flag>10){break;}
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
            <? if(count($news)>0) {?>
            <section> 
                <div class="container">
                    <div class="underline-title">
                        <div>
                             <h4>Latest news</h4>  
                        </div>
                       

                    </div>
                    <div id="news_slider" class="row">
                        <? $this->renderPartial('news',array('news'=>$news)); ?>
                    </div>
                </div>
            </section>
            <? }?>
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
<script type="text/javascript">
            $(document).ready(function(){
                $(".breadcrumb li span").click(function(){
                    var togglediv = $(".home-text-icon-list:nth-child(3)");
                    togglediv.toggle();
                    if(togglediv.css("display")=='block'){
                        $(this).text('- less');
                        $("#rss").height("500px");
                    }else{$(this).text('+ more'); $("#rss").height("300px");}
                });
            });
</script>
