<? $this->pageTitle = Yii::app()->name ?>
    <div class="content">
        <section class="image-background">
            <div class="image-overlay"></div>
            <div class="container">
                <div class="row">
                    <div class="col-xs-10 col-xs-offset-1 text-center">
                        <h1 class="home-search-bar-title">GIGADB DATASETS</h1>
                        <p class="home-search-bar-subtitle">GigaDB contains
                            <? echo $count ?> discoverable, trackable, and citable datasets that have been assigned DOIs and are available for public download and use.</p>
                        <? $this->renderPartial('/search/_form',array('model'=>$form,'dataset'=>$dataset,'search_result'=>null)); ?>
                    </div>
                </div>
            </div>
        </section>
        <section class="mb-20">
            <div class="container">
                <div class="row">
                    <div class="col-xs-8">
                        <div class="underline-title">
                            <button class="pull-right btn btn-link toggle-dataset-types-btn" aria-controls="datasetTypesList" aria-label="Show more dataset types">+ more</button>
                            <div>
                                <h2 class="heading">Dataset types</h2>
                            </div>
                        </div>
                        <ul id="datasetTypesList" class="list-inline home-text-icon-list">
                            <li>
                                <a href="/search/new?keyword=Genomic&type%5B%5D=dataset&dataset_type%5B%5D=Genomic">
                                    <div class="text-icon text-icon-green">
                                        <img src="/images/new_interface_image/Genomic.svg" alt="">
                                    </div>Genomic<span class="sr-only">  datasets</span> (<span><? echo $number_genomic ?></span>)</a>
                            </li>
                            <li>
                                <a href="/search/new?keyword=Software&type%5B%5D=dataset&dataset_type%5B%5D=Software">
                                    <div class="text-icon text-icon-blue">
                                        <img src="/images/new_interface_image/Software.svg" alt="">
                                    </div>Software<span class="sr-only">  datasets</span> (<span><? echo $number_software ?></span>)</a>
                            </li>
                            <li>
                                <a href="/search/new?keyword=Transcriptomic&type%5B%5D=dataset&dataset_type%5B%5D=Transcriptomic">
                                    <div class="text-icon text-icon-blue">
                                        <img src="/images/new_interface_image/Transcriptomic.svg" alt="">
                                    </div>Transcriptomic<span class="sr-only">  datasets</span> (<span><? echo $number_ts ?></span>)</a>
                            </li>
                            <li>
                                <a href="/search/new?keyword=Imaging&type%5B%5D=dataset&dataset_type%5B%5D=Imaging">
                                    <div class="text-icon text-icon-blue">
                                        <img src="/images/new_interface_image/Imaging.svg" alt="">
                                    </div>Imaging<span class="sr-only">  datasets</span> (<span><? echo $number_imaging ?></span>)</a>
                            </li>
                            <li>
                                <a href="/search/new?keyword=Neuroscience&type%5B%5D=dataset&dataset_type%5B%5D=Neuroscience">
                                    <div class="text-icon text-icon-yellow">
                                        <img src="/images/new_interface_image/Neuroscience.svg" alt="">
                                    </div>Neuroscience<span class="sr-only">  datasets</span> (<span><? echo $number_ns ?></span>)</a>
                            </li>
                            <li>
                                <a href="/search/new?keyword=Epigenomic&type%5B%5D=dataset&dataset_type%5B%5D=Epigenomic">
                                    <div class="text-icon text-icon-red">
                                        <img src="/images/new_interface_image/Epigenomic.svg" alt="">
                                    </div>Epigenomic<span class="sr-only">  datasets</span> (<span><? echo $number_epi ?></span>)</a>
                            </li>
                            <li>
                                <a href="/search/new?keyword=Metagenomic&type%5B%5D=dataset&dataset_type%5B%5D=Metagenomic">
                                    <div class="text-icon text-icon-green">
                                        <img src="/images/new_interface_image/Metagenomic.svg" alt="">
                                    </div>Metagenomic<span class="sr-only">  datasets</span> (<span><? echo $number_metagenomic ?></span>)</a>
                            </li>
                            <li>
                                <a href="/search/new?keyword=Genome-Mapping&type%5B%5D=dataset&dataset_type%5B%5D=Genome-Mapping">
                                    <div class="text-icon text-icon-yellow">
                                        <img src="/images/new_interface_image/Genome-Mapping.svg" alt="">
                                    </div>Genome mapping<span class="sr-only">  datasets</span> (<span><? echo $number_genome_mapping ?></span>)</a>
                            </li>
                            <li>
                                <a href="/search/new?keyword=Workflow&type%5B%5D=dataset&dataset_type%5B%5D=Workflow">
                                    <div class="text-icon text-icon-red">
                                        <img src="/images/new_interface_image/Workflow.svg" alt="">
                                    </div>Workflow<span class="sr-only">  datasets</span> (<span><? echo $number_wf ?></span>)</a>
                            </li>
                            <li>
                                <a href="/search/new?keyword=Proteomic&type%5B%5D=dataset&dataset_type%5B%5D=Proteomic">
                                    <div class="text-icon text-icon-yellow">
                                        <img src="/images/new_interface_image/Proteomic.svg" alt="">
                                    </div>Proteomic<span class="sr-only">  datasets</span> (<span><? echo $number_proteomic ?></span>)</a>
                            </li>
                            <li>
                                <a href="/search/new?keyword=Metabarcoding&type%5B%5D=dataset&dataset_type%5B%5D=Metabarcoding">
                                    <div class="text-icon text-icon-yellow">
                                        <img src="/images/new_interface_image/Metabarcoding.svg" alt="">
                                    </div>Metabarcoding<span class="sr-only">  datasets</span> (<span><? echo $number_metabarcoding ?></span>)</a>
                            </li>
                            <li>
                                <a href="/search/new?keyword=Metadata&type%5B%5D=dataset&dataset_type%5B%5D=Metadata">
                                    <div class="text-icon text-icon-red">
                                        <img src="/images/new_interface_image/Metadata.svg" alt="">
                                    </div>Metadata<span class="sr-only">  datasets</span> (<span><? echo $number_metadata ?></span>)</a>
                            </li>
                            <li class="toggleable">
                                <a href="/search/new?keyword=climate&type%5B%5D=dataset&dataset_type%5B%5D=climate">
                                    <div class="text-icon text-icon-green">
                                        <img src="/images/new_interface_image/Climate.svg"  alt="">
                                    </div>Climate<span class="sr-only">  datasets</span> (<span><? echo $number_climate ?></span>)</a>
                            </li>
                            <li class="toggleable">
                                <a href="/search/new?keyword=Network-Analysis&type%5B%5D=dataset&dataset_type%5B%5D=12">
                                    <div class="text-icon text-icon-blue">
                                        <img src="/images/new_interface_image/Network-Analysis.svg" alt="">
                                    </div>Network-Analysis<span class="sr-only">  datasets</span> (<span><? echo $number_na ?></span>)</a>
                            </li>
                            <li class="toggleable">
                                <a href="/search/new?keyword=ElectroEncephaloGraphy(EEG)&type%5B%5D=dataset&dataset_type%5B%5D=15">
                                    <div class="text-icon text-icon-yellow">
                                        <img src="/images/new_interface_image/EEG.svg" alt="">
                                    </div>EEG<span class="sr-only">  datasets</span> (<span><? echo $number_eeg ?></span>)</a>
                            </li>
                            <li class="toggleable">
                                <a href="/search/new?keyword=Phenotyping&type%5B%5D=dataset&dataset_type%5B%5D=21">
                                    <div class="text-icon text-icon-red">
                                        <img src="/images/new_interface_image/Phenotyping.svg" alt="">
                                    </div>Phenotyping<span class="sr-only">  datasets</span> (<span><? echo $number_pt ?></span>)</a>
                            </li>
                            <li class="toggleable">
                                <a href="/search/new?keyword=Metabolomic&type%5B%5D=dataset&dataset_type%5B%5D=8">
                                    <div class="text-icon text-icon-yellow">
                                        <img src="/images/new_interface_image/Metabolomic.svg" alt="">
                                    </div>Metabolomic<span class="sr-only">  datasets</span> (<span><? echo $number_metabolomic ?></span>)</a>
                            </li>
                            <li class="toggleable">
                                <a href="/search/new?keyword=Lipidomic&type%5B%5D=dataset&dataset_type%5B%5D=20">
                                    <div class="text-icon text-icon-green">
                                        <img src="/images/new_interface_image/Lipidomic.svg" alt="">
                                    </div>Lipidomic<span class="sr-only">  datasets</span> (<span><? echo $number_lipi ?></span>)</a>
                            </li>
                            <li class="toggleable">
                                <a href="/search/new?keyword=ecology&type%5B%5D=dataset&dataset_type%5B%5D=19">
                                    <div class="text-icon text-icon-blue">
                                        <img src="/images/new_interface_image/Ecology.svg" alt="">
                                    </div>Ecology<span class="sr-only">  datasets</span> (<span><? echo $number_ecology ?></span>)</a>
                            </li>
                            <li class="toggleable">
                                <a href="/search/new?keyword=Virtual-Machine&type%5B%5D=dataset&dataset_type%5B%5D=14">
                                    <div class="text-icon text-icon-green">
                                        <img src="/images/new_interface_image/Virtual-Machine.svg" alt="">
                                    </div>Virtual-Machine<span class="sr-only">  datasets</span> (<span><? echo $number_vm ?></span>)</a>
                            </li>
                        </ul>
                    </div>
                    <div class="col-xs-4" id="rss" style="height:300px;overflow:scroll;">
                        <div class="underline-title">
                            <div>
                                <h2 class="heading">RSS</h2>
                            </div>
                        </div>
                        <?php $flag=1;foreach($rss_arr as $item)  {?>
                        <? if($flag>10){break;}
                            if(get_class($item) == 'Dataset'){?>
                            <p style="margin-bottom: 0px;">New dataset added on
                                <?=$item->publication_date?>:
                                    <?=CHtml::link("10.5524/".$item->identifier, $item->shortUrl)?>
                                        <?=$item->title?>
                            </p>
                            <?}else{?>
                                <p style="margin-bottom: 0px;">
                                    <?= $item->publication_date ?>:
                                        <?=$item->message?>
                                </p>
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
                            <h2 class="heading">Latest news</h2>
                        </div>
                    </div>
                    <div id="news_slider" class="row">
                        <? $this->renderPartial('news',array('news'=>$news)); ?>
                    </div>
                </div>
            </section>
            <? }?>
                <section>
                <h2 class="sr-only">Data Overview Metrics</h2>
                    <div class="container">
                        <div class="color-background ">
                            <div class="row home-color-background-grid">
                                <div class="col-xs-3">
                                    <div class="home-color-background-block">
                                        <div class="text-icon text-icon-o text-icon-lg">
                                            <img src="/images/new_interface_image/datasets.svg" alt=""></div>
                                        <h3 class="heading"><span class="sr-only">Number of datasets </span><? echo $count ?></h3>
                                        <div aria-hidden="true" class="content">Datasets</div>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <div class="home-color-background-block" onclick="window.location='/site/mapbrowse';">
                                        <div class="text-icon text-icon-o text-icon-lg">
                                            <img src="/images/new_interface_image/samples.svg" alt=""></div>
                                        <h3 class="heading"><span class="sr-only">Number of samples </span><? echo $count_sample ?></h3>
                                        <div aria-hidden="true" class="content">Samples</div>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <div class="home-color-background-block">
                                        <div class="text-icon text-icon-o text-icon-lg">
                                            <img src="/images/new_interface_image/files.svg" alt=""></div>
                                        <h3 class="heading"><span class="sr-only">Number of files </span><? echo $count_file ?></h3>
                                        <div aria-hidden="true" class="content">Files</div>
                                    </div>
                                </div>
                                <div class="col-xs-3">
                                    <div class="home-color-background-block">
                                        <div class="text-icon text-icon-o text-icon-lg">
                                            <img src="/images/new_interface_image/volume.svg" alt=""></div>
                                        <h3 class="heading"><span class="sr-only">Total Volume of Data </span>31</h3>
                                        <div aria-hidden="true" class="content">Data volume(TB)</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
    </div>
    <script src="https://hypothes.is/embed.js" async></script>
    <script>
    document.addEventListener("DOMContentLoaded", function(event) {
        $("#dataset-hint").popover();

        let showAllDatasetTypes = false;
        const toggleableDatasetTypes = $("#datasetTypesList li:nth-child(n+13)");

        $(document).ready(function toggleExpandContent() {
            $("[aria-controls='datasetTypesList']").on("click", function() {
                toggleableDatasetTypes.toggle(0, function() {
                    if ($(this).is(':visible')) {
                        $(this).css('display', 'inline-block');
                    }
                })
                showAllDatasetTypes = !showAllDatasetTypes

                if (showAllDatasetTypes) {
                    $(this).text('- less');
                    $(this).attr('aria-label', 'Show less dataset types')
                    $("#rss").height("500px");
                } else {
                    $(this).text('+ more');
                    $(this).attr('aria-label', 'Show more dataset types')
                    $("#rss").height("300px");
                }
            });
        });
    });
</script>