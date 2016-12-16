<?
if(Yii::app()->user->hasFlash('saveSuccess'))
    echo Yii::app()->user->getFlash('saveSuccess');

$cs = Yii::app()->getClientScript();
$cssCoreUrl = $cs->getCoreScriptUrl();
Yii::app()->clientScript->registerScriptFile('/js/jquery-migrate-1.2.1.js', CClientScript::POS_END);
Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js');
Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js');
Yii::app()->clientScript->registerScriptFile('/js/jquery.tag-editor.min.js');
Yii::app()->clientScript->registerScriptFile('/js/jquery.caret.min.js');

$cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
$cs->registerCssFile('/css/jquery.tag-editor.css');

?>
<?php $form=$this->beginWidget('CActiveForm', array(
    'id'=>'dataset-form',
    'enableAjaxValidation'=>false,
    'htmlOptions'=>array(
        'class'=>'form-horizontal',
        'enctype'=>'multipart/form-data'),
)); ?>
<div class="span12 form well">
    <div class="form-horizontal">
        <p class="note">Fields with <span class="required">*</span> are required.</p>
        <div class="clear"></div>
        <?php echo $form->errorSummary($model); ?>

        <div class="container">

            <div class="row">
                <div class="span6">
                    <div class="control-group">
                        <?php echo $form->labelEx($model,'submitter_id',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->dropDownList($model,'submitter_id',MyHtml::listData(User::model()->findAll(array('order'=>'email ASC')),'id','email')); ?>
                            <?php echo $form->error($model,'submitter_id'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <?php echo $form->labelEx($model,'upload_status',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->dropDownList($model,'upload_status',Dataset::$statusList,
                                array('class'=>'js-pub', 'disabled'=>$model->upload_status == 'Published')); ?>
                            <?php echo $form->error($model,'upload_status'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <?php echo $form->labelEx($model,'types',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?
                                $datasetTypes = MyHtml::listData(Type::model()->findAll(),'id','name');
                                $checkedTypes = MyHtml::listData($model->datasetTypes,'id','id');
                                foreach ($datasetTypes as $id => $datasetType) {
                                    $checkedHtml = in_array($id,$checkedTypes,true) ? 'checked="checked"' : '';
                                    echo '<input type="checkbox" name="datasettypes['.$id.']" value="1"'.$checkedHtml.'/> '.$datasetType.'<br/>';
                                }
                            ?>
                        </div>
                    </div>


                    <div class="control-group">
                        <?php echo $form->labelEx($model,'dataset_size',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model,'dataset_size',array('size'=>60,'maxlength'=>200)); ?> (bytes)
                            <?php echo $form->error($model,'dataset_size'); ?>
                        </div>
                    </div>

                </div>

                <div class="span6">
                    <?
                        $img_url = $model->image->image('image_upload');
                        $fn = '' ;
                        if($img_url){
                            $fn = explode('/' , $img_url);
                            $fn = end($fn);
                        }
                    ?>
                    <? echo ($img_url && $fn !='Images_.png') ? MyHtml::image($img_url, $img_url, array('style'=>'width:100px; margin-left:160px;margin-bottom:10px;')) : ''; ?>
                    <div class="control-group">
                        <?php echo $form->labelEx($model->image,'Image Upload',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $model->image->imageChooserField('image_upload'); ?>
                            <?php echo $form->error($model->image,'image_upload'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <?php echo $form->labelEx($model->image,'url',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model->image,'url',array('size'=>60,'maxlength'=>200)); ?>
                            <?php echo $form->error($model->image,'url'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model->image,'source',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model->image,'source',array('size'=>60,'maxlength'=>200)); ?>
                            <?php echo $form->error($model->image,'source'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model->image,'tag',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model->image,'tag',array('size'=>60,'maxlength'=>200)); ?>
                            <?php echo $form->error($model->image,'tag'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model->image,'license',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model->image,'license',array('size'=>60,'maxlength'=>200)); ?>
                            <?php echo $form->error($model->image,'license'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model->image,'photographer',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model->image,'photographer',array('size'=>60,'maxlength'=>200)); ?>
                            <?php echo $form->error($model->image,'photographer'); ?>
                        </div>
                    </div>
                    <div class="row">
                        <fieldset class="form-inline">

                            <div class="control-group">
                                <div class="span1">
                                    <?php echo $form->labelEx($model,'identifier',array('class'=>'control-label')); ?>
                                </div>
                                <div class="controls">
                                    <div class="span1">
                                        <?php echo $form->textField($model,'identifier',array('size'=>32,
                                                                                                'maxlength'=>32,
                                                                                                'disabled'=>$model->upload_status == 'Published',
                                                                                                'class' => "input-mini",
                                                                                                'ajax' => array(
                                                                                                    'type' => 'POST',
                                                                                                    'url' => array('dataset/checkDOIExist'),
                                                                                                    'dataType' => 'JSON',
                                                                                                    'data'=>array('doi'=>'js:$(this).val()'),
                                                                                                    'success'=>'function(data){
                                                                                                        if(data.status){
                                                                                                            $("#Dataset_identifier").addClass("error");
                                                                                                        }else {
                                                                                                            $("#Dataset_identifier").removeClass("error");

                                                                                                        }
                                                                                                    }',
                                                                                                ),
                                                                                                )); ?>
                                        <?php echo $form->error($model,'identifier'); ?>
                                    </div>

                                    <div class="span3">
                                        <?php
                                        echo CHtml::ajaxLink('Mint DOI',Yii::app()->createUrl('/dataset/mint/'),
                                        array(
                                            'type'=>'POST',
                                            'data'=> array('doi'=>'js:$("#Dataset_identifier").val()'),
                                            'dataType'=>'json',
                                            'success'=>'js:function(output){
                                                console.log(output);
                                                if(output.status){
                                                    $("#minting").html("new DOI successfully minted");

                                                }else {
                                                    $("#minting").html("error minting a DOI: "+ output.md_curl_status + ", " + output.doi_curl_status);
                                                }
                                                $("#mint_doi_button").toggleClass("active");
                                            }',
                                        ),array('class'=>'btn btn-green',
                                                'id' =>'mint_doi_button',
                                        ));

                                        ?>
                                        <div id="minting"></div>
                                    </div>

                                </div>
                            </div>



                        </fieldset>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'ftp_site',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model,'ftp_site',array('size'=>60,'maxlength'=>200, 'disabled'=>$model->upload_status == 'Published')); ?>
                            <?php echo $form->error($model,'ftp_site'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'publisher',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->dropDownList($model,'publisher_id',MyHtml::listData(Publisher::model()->findAll(),'id','name')); ?>
                            <?php echo $form->error($model,'publisher_id'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'fairnuse',array('class'=>'control-label')); ?>
                        <div class="controls">
                        <?php echo $form->textField($model,'fairnuse',array('class'=>'date')); ?>
                        <?php echo $form->error($model,'fairnuse'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'publication_date',array('class'=>'control-label')); ?>
                        <div class="controls">
                        <?php echo $form->textField($model,'publication_date',array('class'=>'date js-date-pub', 'disabled'=>$model->upload_status == 'Published')); ?>
                        <?php echo $form->error($model,'publication_date'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'modification_date',array('class'=>'control-label')); ?>
                        <div class="controls">
                        <?php echo $form->textField($model,'modification_date',array('class'=>'date')); ?>
                        <?php echo $form->error($model,'modification_date'); ?>
                        </div>
                    </div>

                </div>

            </div> <!-- end of row of two columns -->

            <div class="row">

                <div class="span12">

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'title',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model,'title',array('class'=>'span8', 'size'=>60,'maxlength'=>300)); ?>
                            <?php echo $form->error($model,'title'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'description',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textArea($model,'description',array('class'=>'span8','rows'=>8, 'cols'=>50)); ?>
                            <?php echo $form->error($model,'description'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo CHtml::label('Keywords','keywords', array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo CHtml::textField('keywords', '', array('class'=>'span8', 'size'=>60,'maxlength'=>300)); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo CHtml::label('URL to redirect','urltoredirect', array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo CHtml::textField('urltoredirect', $model->getUrlToRedirectAttribute(), array('class'=>'span8', 'size'=>60,'maxlength'=>300)); ?>
                        </div>
                    </div>
                </div>
            </div> <!-- end of row of one column -->

        </div> <!-- end of container -->

    </div>
</div>

<script language="javascript">
function checkdate() {



    var date= document.getElementById("pdate").value;
    var current = new Date();
    var month = current.getMonth()+1;

    var today = current.getFullYear()+'-'+month + '-'+current.getDate();


    if(date !== today)
    {
        var r= window.confirm("The publication date is currently "+ date+", Do you want this changed to todays date "+ today);
        if(r==true) {

            document.getElementById("pdate").value=today;
        }else {


        }

    }

}

</script>
<div class="span12" style="text-align:center">
    <a href="<?=Yii::app()->createUrl('/dataset/admin')?>" class="btn"/>Cancel</a>
    <?= CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn-green','onclick'=>'js:checkdate()')); ?>
        <? if (!$model->isNewRecord && ($model->upload_status != 'Published')) { ?>
    <a href="<?=Yii::app()->createUrl('/dataset/private/identifier/'.$model->identifier)?>" class="btn-green"/>Create/Reset Private URL</a>
        <?if($model->token){?>
        <a href="<?= Yii::app()->createUrl('/dataset/view/id/'.$model->identifier.'/token/'.$model->token) ?>">Open Private URL</a>
        <?}?>
        <? } ?>
</div>
<?php $this->endWidget(); ?>
<script type="text/javascript">

$(function() {

    var publication_date = $('.js-date-pub');
    var months = new Array('Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec');

    function today() {
        var d = new Date();
        return new Array(
            ("0" + d.getDate()).slice(-2) + '-' + months[d.getMonth()] + '-' + d.getFullYear(),
            d.getFullYear() + '-' + ("0" + (d.getMonth() + 1)).slice(-2) + '-' + ("0" + d.getDate()).slice(-2)
        );
    }

    //$("#myModal").modal();
    $('.date').datepicker({'dateFormat': 'yy-mm-dd'});

    // On Published show modal if date != date today
    $('.js-pub').on('change', function(e) {
        if ($(this).val() === 'Published') {
            var d = today();
            if (publication_date.val() && publication_date.val() !== d[1]) {
                var current = new Date(publication_date.val());
                var textDate = ("0" + current.getDate()).slice(-2) + '-' + months[current.getMonth()] + '-' + current.getFullYear();
                $("#current").text(textDate);
                $("#today").text(d[0]);
                $("#myModal").modal('show');
            } else if (!publication_date.val()) {
                publication_date.val(d[1]);
            }
        }
    });

    // Change the publication date with date today
    $('.changeToday').on('click', function(e) {
        var d = today();
        publication_date.val(d[1]);
        $("#myModal").modal('hide');
    });

});

</script>

<script>
<?php
$js_array = json_encode($model->getSemanticKeywords());
echo "var existingTags = ". $js_array . ";\n";
?>
    $('#keywords').tagEditor({
    initialTags:
        existingTags,
    delimiter: ',', /* comma */
    placeholder: 'Enter keywords (separated by commas) ...'
});

$(function(){
    $('#mint_doi_button').click(function() {
        $('#minting').html('minting under way, please wait');
        $(this).toggleClass('active');
    });
});

</script>



<!-- Button to trigger modal -->
<!--<a href="#myModal" role="button" class="btn" data-toggle="modal">Launch demo modal</a> -->

<!-- Modal -->
<!--
<div id="myModal" class="modal hide fade" tabindex="-1" role="dialog" aria-labelledby="myModalLabel" aria-hidden="true">
  <div class="modal-header">
    <button type="button" class="close" data-dismiss="modal" aria-hidden="true">×</button>
    <h3 id="myModalLabel">Upload Status</h3>
  </div>
  <div class="modal-body model-body-text">
    <p>The publication date is currently <strong><span id="current"></span></strong> do you want to change this changed to today? <strong><span id="today"></span></strong></p>
  </div>
  <div class="modal-footer">
    <button class="btn" data-dismiss="modal" aria-hidden="true">Do NOT release</button>
    <button class="btn btn-primary changeToday">Change to today</button>
  </div>
</div>
-->
