<div class="filter-your-result">Filter your result</div>

<form id="filter_form" method="GET">
<input type="hidden" name="keyword" value="<?= $model->keyword ?>"/>

<!-- FILTERS FOR DATASETS -->
    <div id="dataset_filter">
        <div class="filter">
            <h4 class='heading'><?=Yii::t('app' , 'Type')?></h4>
            <div id="result_type" class='filter-content' style="<?= $model->type ? 'display:block;': 'display:none;'?>">
                <button class="btn btn_filter" id="btn_type"><? if(empty($model->type)) echo Yii::t('app' , 'Enable All'); else echo Yii::t('app' , 'Disable'); ?></button>
                <div class="options <? if(empty($model->type)) echo 'disabled'; ?> ">
                    <? echo CHtml::checkBoxList("type",$model->type, array('dataset'=>'Dataset', 'sample'=>'Sample', 'file'=>'File'),array('class'=>'type')); ?>
                </div>
            </div>
        </div>

    </div>

<!-- FILTERS FOR FILES -->

    <div id="file_filter">
    </div>



    <?php
        echo CHtml::submitButton(Yii::t('app' ,'Apply Filter'), array('class'=>'span2 btn-green filter'));
        echo CHtml::endForm();
    ?>

<script>
document.addEventListener("DOMContentLoaded", function(event) { //This event is fired after deferred scripts are loaded
    $(function(){
        $("#result_type").show();
        //toggle the componenet with class msg_body
        $(".heading").click(function()
        {
          $(this).next(".filter-content").slideToggle(500);
        });
        /*$('#filter_form input:checkbox').click(function(){
            document.forms['filter_form'].submit();
        });*/
    });

    submitFilter = function(){
        var action=$(this).attr("action");
        var tab=$("#filter_tab").val();
        if(tab!="" && action.indexOf("#") ==-1){
            action=action+tab;
        }
        $(this).attr("action",action);
        $(this).submit();
        return false;
    };

    $(function () {

        $('.btn_filter').click(function () {
            var action=$(this).html();
            var alt="";
            if($(this).next().has("input:text").length==0){
                alt="Enable All";
            }else{
                alt="Enable";
            }

            var status;
            if(action=='<?=Yii::t('app' , 'Disable')?>'){
                $(this).html(alt);
                status=false;
                $(this).next().addClass('disabled');
            }else {
                $(this).html('<?=Yii::t('app' , 'Disable')?>');
                status=true;
                $(this).next().removeClass('disabled');
            }
            $(this).next().find(':checkbox').attr('checked', status);
            $(this).next().find(':text').attr('value', "");
            document.forms['filter_form'].submit(submitFilter);
            return false;
        });

        $('input:checkbox').click(function (e) {
            var disable=true;
            $(this).parent().children("input:checkbox").each(function(index,ele){
                if($(ele).attr("checked")){
                    $(ele).parent().parent().parent().children("button").html('<?=Yii::t('app' , 'Disable')?>');
                    $(ele).parent().parent().removeClass('disabled');
                    disable = false;
                }
            });
            if(disable){
                $(this).parent().parent().parent().children("button").html("Enable All");
                $(this).parent().parent().addClass('disabled');
            }
        });

    });


    $('#filter_form').submit(submitFilter);

});
</script>
