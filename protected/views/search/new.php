<div class="content">
            <section class="image-background">
                <div class="container">
                    <div class="row">
                        <div class="col-xs-10 col-xs-offset-1 text-center home-search-bar-content">
                            <h1 class="home-search-bar-title">SEARCH GIGADB DATASETS</h1>
                            <? $this->renderPartial('_search', array('model' => $model))?>
                        </div>
                    </div>
                </div>
            </section>
            <div class="container">
                <div class="row">

                    <div class="col-xs-4 search-filter-sidebar">
                        <h2 class="h4 search-result-title" id="search_result_heading">Search result for <span><i><?php echo $model->keyword ?></i></span></h2>
                          <p><?php $this->renderPartial('_range', array(
                                    'total_dataset'=>$datasets['total'],
                                    'page'=>$page,
                                    'limit'=>$limit
                              ));?> </p>

                           <section>
                                <?php $this->renderPartial("_filter", array(
                                          'model' => $model,
                                          'list_dataset_types' => $list_dataset_types,
                                )) ?>
                            </section>
                             </div>

                    <section class="subsection col-xs-8" aria-labelledby="search_result_heading">
                        <div class="span9 result" id="result">
        <!--<span class='pull-right'><?= Yii::t('app', 'Selected all files') ?> <input type="checkbox" class="select-all"/></span> -->
                            <?php $this->renderPartial("_new_result", array(
                                 'model' => $model,
                                 'datasets' => $datasets,
                                 'samples' => $samples,
                                  'files' => $files,
                                'display' => $display
                                 )) ?>
                         </div>
                        <div class="row1">
                            <div class="span9 offset3" style="margin-left: 50px;height:72px;">
                              <ul id="search-pg" class="pagination-sm" style="margin: auto;width: 100%"></ul>
                            </div>
                        </div>
                    </section>

            </div>
        </div>

    <br>
    <br>

<script>
document.addEventListener("DOMContentLoaded", function(event) { //This event is fired after deferred scripts are loaded
    $(".hint").tooltip({'placement':'left'});
    $(".content-popup").popover({'placement':'right'});
    $('#myTab a').click(function (e) {
        e.preventDefault();
        $(this).tab('show');
        var rel=$(this).attr("rel");
        $(rel).show();
        if(rel=="#file_filter"){
            //window.location.hash="#result_files";
            $("#filter_tab").val("#result_files");
            $("#dataset_filter").hide();
        }else if(rel=="#dataset_filter"){
            $("#file_filter").hide();
            //window.location.hash="#result_dataset";
            $("#filter_tab").val("#result_dataset");
        }

    });

    if(window.location.hash=="#result_files"){
        $("#myTab a[href='#result_files']").tab("show");
        $("#file_filter").show();
        $("#dataset_filter").hide();

    }
});
</script>

<script src="https://cdnjs.cloudflare.com/ajax/libs/twbs-pagination/1.4.2/jquery.twbsPagination.min.js" defer></script>
<script>
document.addEventListener("DOMContentLoaded", function(event) { //This event is fired after deferred scripts are loaded
  $("#search-pg").twbsPagination({
        totalPages: <?php echo $total_page ?>,
        visiblePages: 5,
        onPageClick: function (event, page) {
            url = document.URL;
            $.post(url, {'page': page}, function(result) {
                if(result.success) {
                    $('#filter').html(result.filter);
                    $('#result').html(result.result);
                    $('#range').html(result.range);
                }
            }, 'json');
        }
    });
});
</script>
