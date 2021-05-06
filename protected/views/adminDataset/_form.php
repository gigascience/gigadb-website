<?
if(Yii::app()->user->hasFlash('saveSuccess'))
    echo Yii::app()->user->getFlash('saveSuccess');


$cs = Yii::app()->getClientScript();
$cssCoreUrl = $cs->getCoreScriptUrl();

$cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
$cs->registerCssFile('/css/jquery.tag-editor.css');

?>
<?php if (Yii::app()->user->hasFlash('error')) { ?>
    <div class="alert alert-danger" role="alert">
        <?php echo Yii::app()->user->getFlash('error'); ?>
    </div>
<? } ?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/caret/1.0.0/jquery.caret.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tag-editor/1.0.20/jquery.tag-editor.min.js"></script>

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
                <div class="span4">
                    <div class="control-group">
                        <?php echo $form->labelEx($model,'submitter_id',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->dropDownList($model,'submitter_id',CHtml::listData(User::model()->findAll(array('order'=>'email ASC')),'id','email')); ?>
                            <?php echo $form->error($model,'submitter_id'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <?php echo $form->labelEx($model,'curator_id',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php
                            $criteria = new CDbCriteria;
                            $criteria->condition='role=\'admin\' and email like \'%gigasciencejournal.com\''; 
                            ?>
                            <?php echo $form->dropDownList($model,'curator_id',CHtml::listData(User::model()->findAll($criteria),'id','email'),array('prompt'=>'')); ?>
                            <?php echo $form->error($model,'curator_id'); ?>
                        </div>
                    </div>
                     <div class="control-group">
                        <?php echo $form->labelEx($model,'manuscript_id',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model,'manuscript_id',array('size'=>60,'maxlength'=>200)); ?>
                            <?php echo $form->error($model,'manuscript_id'); ?>
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
                        <fieldset class="checkboxes">
                                <legend class="checkboxes-legend">Types</legend>
                                <?
                                    $datasetTypes = CHtml::listData(Type::model()->findAll(),'id','name');
                                    $checkedTypes = CHtml::listData($model->datasetTypes,'id','id');
                                    foreach ($datasetTypes as $id => $datasetType) {
                                        // echo '<div class="control-group">';
                                        echo $form->labelEx($model,"$datasetType",array('class'=>'checkbox-label'));
                                        $checkedHtml = in_array($id,$checkedTypes,true) ? 'checked="checked"' : '';
                                        $checkboxId="Dataset_$datasetType";
                                        echo '<div class="controls">';
                                        echo '<input id="'.$checkboxId.'" type="checkbox" name="datasettypes['.$id.']" value="1"'.$checkedHtml.'/>';
                                        echo '</div>';
                                        // echo '</div>';

                                    }
                                ?>
                        </fieldset>
                    </div>



                    <div class="control-group">
                        <?php echo $form->labelEx($model,'dataset_size',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model,'dataset_size',array('size'=>60,'maxlength'=>200)); ?> (bytes)
                            <?php echo $form->error($model,'dataset_size'); ?>
                        </div>
                    </div>

                </div>
                <div class="span1">
                    &nbsp;
                </div>
                <div class="span5">
                    <?
                        $img_url = $model->image->image('image_upload');
                        $fn = '' ;
                        if($img_url){
                            $fn = explode('/' , $img_url);
                            $fn = end($fn);
                        }
                    ?>
                    <? echo ($img_url && $fn !='Images_.png') ? CHtml::image($img_url, $img_url, array('style'=>'width:100px; margin-left:160px;margin-bottom:10px;')) : ''; ?>
                    <div class="control-group">
                        <label for="image_upload_image" class="control-label">Image Upload</label>
                        <div class="controls">
                            <?php echo $model->image->imageChooserField('image_upload'); ?>
                            <?php echo $form->error($model->image,'image_upload'); ?>
                        </div>
                    </div>
                    <div class="control-group">
                        <?php echo $form->labelEx($model->image,'url',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model->image,'url',array('class'=>'span4','size'=>60,'maxlength'=>200)); ?>
                            <?php echo $form->error($model->image,'url'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model->image,'source',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model->image,'source',array('class'=>'span4','size'=>60,'maxlength'=>200)); ?>
                            <?php echo $form->error($model->image,'source'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model->image,'tag',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model->image,'tag',array('class'=>'span4','size'=>60,'maxlength'=>200)); ?>
                            <?php echo $form->error($model->image,'tag'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model->image,'license',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model->image,'license',array('class'=>'span4','size'=>60,'maxlength'=>200)); ?>
                            <?php echo $form->error($model->image,'license'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model->image,'photographer',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model->image,'photographer',array('class'=>'span4','size'=>60,'maxlength'=>200)); ?>
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
                                        <?php echo $form->textField($model,'identifier',
                                            array('size'=>32,
                                                'maxlength'=>32,
                                                'disabled'=>$model->upload_status == 'Published',
                                                'class' => "input-mini",
                                                'ajax' => array(
                                                    'type' => 'POST',
                                                    'url' => array('adminDataset/checkDOIExist'),
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
                                            )
                                        ); ?>
                                        <?php echo $form->error($model,'identifier'); ?>
                                    </div>

                                    <div class="span2">
                                        <?php
                                        $status_array = array('Submitted', 'UserStartedIncomplete', 'Curation');
                                        echo CHtml::ajaxLink('Mint DOI',Yii::app()->createUrl('/adminDataset/mint/'),
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
                                                'disabled'=>in_array($model->upload_status, $status_array),
                                                
                                        ));

                                        ?>
                                        <div id="minting"></div>
                                    
                                        <?php
                                            if("Curation" === $model->upload_status) {
                                                echo CHtml::link("Move files to public ftp",
                                                    "/adminDataset/moveFiles/doi/{$model->identifier}",
                                                    ["class" => "btn btn-green btn-mini", "style"=>"margin-left:2px;margin-top:2px;"]);
                                            }
                                        ?>
                                    </div>

                                </div>
                            </div>



                        </fieldset>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'ftp_site',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model,'ftp_site',array('class'=>'span4','size'=>60,'maxlength'=>200, 'disabled'=>$model->upload_status == 'Published')); ?>
                            <?php echo $form->error($model,'ftp_site'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'fairnuse',array('class'=>'control-label')); ?>
                        <div class="controls">
                        <?php echo $form->textField($model,'fairnuse',array('class'=>'span4 date')); ?>
                        <?php echo $form->error($model,'fairnuse'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'publication_date',array('class'=>'control-label')); ?>
                        <div class="controls">
                        <?php echo $form->textField($model,'publication_date',array('class'=>'span4 date js-date-pub', 'disabled'=>$model->upload_status == 'Published')); ?>
                        <?php echo $form->error($model,'publication_date'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'modification_date',array('class'=>'control-label')); ?>
                        <div class="controls">
                        <?php echo $form->textField($model,'modification_date',array('class'=>'span4 date')); ?>
                        <?php echo $form->error($model,'modification_date'); ?>
                        </div>
                    </div>

                </div>

            </div> <!-- end of row of two columns -->

            <div class="row">

                <div class="span9">

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'title',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textField($model,'title',array('class'=>'span10', 'size'=>60,'maxlength'=>300)); ?>
                            <?php echo $form->error($model,'title'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo $form->labelEx($model,'description',array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo $form->textArea($model,'description',array('class'=>'span10','rows'=>8, 'cols'=>50)); ?>
                            <?php echo $form->error($model,'description'); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo CHtml::label('Keywords','keywords', array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo CHtml::textField('keywords', '', array('class'=>'span10', 'size'=>60,'maxlength'=>300)); ?>
                        </div>
                    </div>

                    <div class="control-group">
                        <?php echo CHtml::label('URL to redirect','urltoredirect', array('class'=>'control-label')); ?>
                        <div class="controls">
                            <?php echo CHtml::textField('urltoredirect', $model->getUrlToRedirectAttribute(), array('class'=>'span10', 'size'=>60,'maxlength'=>300)); ?>
                        </div>
                    </div>
                </div>
            </div> <!-- end of row of one column -->
            
           <!-- <?php echo CHtml::link('Curation Log', $this->createAbsoluteUrl('curationlog/admin',array('id'=>$model->id))); ?> -->
            
            <?php if ( isset($dataset_id) ) {
                echo $this->renderPartial("curationlog",array('dataset_id'=>$dataset_id,'model'=>$curationlog));
            }
            ?>

        </div> <!-- end of container -->

    </div>
</div>

<div class="span12" style="text-align:center">
    <a href="<?=Yii::app()->createUrl('/adminDataset/admin')?>" class="btn"/>Cancel</a>
    <?= CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn-green')); ?>
        <?php if( "hidden" === $datasetPageSettings->getPageType() ) { ?>
    <a href="<?=Yii::app()->createUrl('/adminDataset/private/identifier/'.$model->identifier)?>" class="btn-green"/>Create/Reset Private URL</a>
            <?php if($model->token){?>
            <a href="<?= Yii::app()->createUrl('/dataset/'.$model->identifier.'/token/'.$model->token) ?>">Open Private URL</a>
            <?php }?>
        <?php } elseif ( "mockup" === $datasetPageSettings->getPageType() ) { 
                echo CHtml::link('Generate mockup for reviewers','#', array('class' => 'btn btn-primary', 'data-toggle' => "modal", 'data-target' => "#mockupCreation"));
            }
            ?>

</div>
<?php $this->endWidget(); ?>
<div class="modal fade" id="mockupCreation" tabindex="-1" role="dialog" aria-labelledby="generateMockup">
  <div class="modal-dialog" role="document">
    <div class="modal-content">
      <div class="modal-header">
        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
        <h4 class="modal-title">Generate unique and time-limited mockup url for reviewers</h4>
      </div>
    <?php echo CHtml::beginForm("/adminDataset/mockup/id/".$model->id,"POST",["id" =>"mockupform"]); ?>
      <div class="modal-body">
            <label for="reviewerEmail" class="control-label">Reviewer's email</label>
            <input type="text" name="revieweremail" class="form-control" />
            <div class="btn-group" data-toggle="buttons">
              <label class="btn btn-primary active">
                <input type="radio" name="monthsofvalidity" id="nbMonths1" value="1" autocomplete="off" checked>1 month
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="monthsofvalidity" id="nbMonths3" value="3" autocomplete="off">3 months
              </label>
              <label class="btn btn-primary">
                <input type="radio" name="monthsofvalidity" id="nbMonths6" value="6" autocomplete="off">6 months
              </label>
            </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-default" data-dismiss="modal">Close</button>
        <?php echo CHtml::submitButton("Generate mockup",[ "class" => "btn-green mockup"]); ?>
      </div>
    <?php echo CHtml::endForm(); ?>
    </div><!-- /.modal-content -->
  </div><!-- /.modal-dialog -->
</div><!-- /.modal -->
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
