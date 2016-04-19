<?php //There are search results ?>
<?php $this->renderPartial('_search', array('model' => $model))?>

<h3 class="search-result-title">Search result for <span><i><?php echo $model->keyword ?></i></span></h3>
<div id="range">
<?php $this->renderPartial('_range', array(
        'total_dataset'=>$datasets['total'],
        'page'=>$page,
        'limit'=>$limit
        ));?>
</div>
<div class="row" id="form_result">
    <div class="span3" id="filter">
        <? $this->renderPartial("_filter", array(
            'model' => $model,
            'list_dataset_types' => $list_dataset_types,
            'list_projects' => $list_projects,
            'list_ext_types' => $list_ext_types,
            'list_filetypes' => $list_filetypes,
            'list_formats' => $list_formats,
            'list_common_names' => $list_common_names
        )) ?>
    </div>

    <div class="span9 result" id="result">
        <!--<span class='pull-right'><?= Yii::t('app', 'Selected all files') ?> <input type="checkbox" class="select-all"/></span> -->
        <?php $this->renderPartial("_result", array(
            'model' => $model,
            'datasets' => $datasets,
            'samples' => $samples,
            'files' => $files,
            'display' => $display
        )) ?>
    </div>

</div>
<div class="row">
    <div class="span9 offset3">
        <ul id="search-pg" class="pagination-sm"></ul>
    </div>
</div>

<script>
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
</script>

<?php Yii::app()->clientScript->registerScriptFile('/js/jquery.twbsPagination.min.js', CClientScript::POS_END);?>
<?php
    $script = <<<EO_SCRIPT
    $("#search-pg").twbsPagination({
        totalPages: $total_page,
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
EO_SCRIPT;
Yii::app()->clientScript->registerScript('my_pagination', $script, CClientScript::POS_READY);?>
