<?php //There are search results ?>

<h3 class="search-result-title">Search result for <span><i><?= $author->name ?></i></span></h3>

<div class="row" id="form_result">
    <div class="span3" id="filter">
        
    </div>

    <div class="offset3 span9 result" id="result">
        <!--<span class='pull-right'><?= Yii::t('app', 'Selected all files') ?> <input type="checkbox" class="select-all"/></span> -->
        <?php $this->renderPartial("_author", array(
            'datasets' => $datasets
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
