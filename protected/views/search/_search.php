<?php echo CHtml::beginForm('/search/new','GET',array('class'=>'form','onsubmit'=>'return validateForm(this);','role'=>'search')); ?>
<?php echo CHtml::errorSummary($model); ?>
<div class="form-group home-search-bar-group">
    <div class="input-group search-bar-group">
        <?php

        $this->widget('application.components.DeferrableCJuiAutoComplete', array(
            'name'=>'keyword',
            //'source'=>array('ac1', 'ac2', 'ac3'),
            // 'source'=> array_values($dataset->getListTitles()),
            'source'=> array_values(array()),
            // additional javascript options for the autocomplete plugin
            'options'=>array(
                 'minLength'=>'2',
             ),
            'htmlOptions'=>array(
                'title'=>'Search GigaDB',
                'class'=>'form-control',
             ),
             'value'=>$model->keyword
        ));
          ?>
        <span class="input-group-btn">
            <button class="btn background-btn" type="submit"><i class="fa fa-search"></i> Search again</button>
<!--TODO: Will re-implement the save search function in ticket #1168-->
//    <?
//        if(!Yii::app()->user->isGuest) {
//    ?>
<!--            <input type="button" id="save-search-criteria" class="btn background-btn" value="Save current search criteria"/>-->
<!--    --><?//
//        }
//    ?>
        </span>
    </div>
</div>
<?php echo CHtml::endForm(); ?>
<div id="saveSearchStatus">
    <div id="saveSearchSuccess" style="display:none">
        <h2>Success</h2>
    </div>
    <div id="saveSearchFail" style="display:none">
        <h2>Opps!</h2>
        <h2 id="saveSearchReason">Reason</h2>
    </div>
</div>
<script>
document.addEventListener("DOMContentLoaded", function(event) { //This event is fired after deferred 
    $('#save-search-criteria').on('click', function(e) {
        jQuery.ajax({
            'type': 'POST',
            'dataType': 'json',
            'data': { 'criteria': '\x7B\x22keyword\x22\x3A\x22penguin\x22,\x22criteria\x22\x3A\x22\x22,\x22tab\x22\x3A\x22\x22,\x22query_result\x22\x3A\x22\x22,\x22type\x22\x3A\x5B\x5D,\x22dataset_type\x22\x3A\x5B\x5D,\x22publisher\x22\x3A\x5B\x5D,\x22project\x22\x3A\x5B\x5D,\x22pubdate_from\x22\x3A\x22\x22,\x22pubdate_to\x22\x3A\x22\x22,\x22moddate_from\x22\x3A\x22\x22,\x22moddate_to\x22\x3A\x22\x22,\x22author_id\x22\x3A\x22\x22,\x22common_name\x22\x3A\x5B\x5D,\x22external_link_type\x22\x3A\x5B\x5D,\x22exclude\x22\x3A\x22\x22,\x22file_type\x22\x3A\x5B\x5D,\x22file_format\x22\x3A\x5B\x5D,\x22reldate_from\x22\x3A\x22\x22,\x22reldate_to\x22\x3A\x22\x22,\x22size_from\x22\x3A\x22\x22,\x22size_to\x22\x3A\x22\x22,\x22size_from_unit\x22\x3A\x22\x22,\x22size_to_unit\x22\x3A\x22\x22\x7D', 'result': '\x7B\x22files\x22\x3A\x5B\x5D,\x22samples\x22\x3A\x5B\x5D,\x22datasets\x22\x3A\x5B210\x5D\x7D' },
            'success': function(data) {
                if (data.status == 'fail') {
                    alert('Failed to save: ' + data.reason);
                } else {
                    alert('Successfully save search query');
                }
            },
            'url': '\x2Fsearch\x2Fsave',
            'cache': false
        });
        return false;

    });

    function validateForm(myform) {
        if (myform.keyword.value.length == 0) {
            alert("Keyword can not be blank");
            return false;
        }

        return true;

    }


});
</script>