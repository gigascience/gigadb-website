<?
if (Yii::app()->user->hasFlash('saveSuccess'))
    echo Yii::app()->user->getFlash('saveSuccess');

$cs = Yii::app()->getClientScript();
$cssCoreUrl = $cs->getCoreScriptUrl();
Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js');
$cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
?>
<div class="span12 form well">
    <div class="form-horizontal">
        <p class="note">Fields with <span class="required">*</span> are required.</p>
        <div class="clear"></div>
        <?php echo $form->errorSummary($model); ?>
        <div class="span5">
            <div class="control-group">
                <?php echo $form->labelEx($model, 'submitter_id', array('class' => 'control-label')); ?>
                <div class="controls">

                    <?php
                    $email = Yii::app()->user->getEmail();
                    echo CHtml::textField("email", $email, array('size' => 60, 'maxlength' => 300, 'readonly' => "readonly")
                    );
                    ?> 
                </div>
            </div>

            <div class="control-group">
                <?php echo $form->labelEx($model, 'types', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="Select the type of data to be included 
                   in this submission, you may select more than 1. If a 
                   data type is missing please contact us on database@gigasciencejournal.com."></a>
                <div class="controls">
                    <?
                    $datasetTypes = MyHtml::listData(Type::model()->findAll(), 'id', 'name');
                    foreach ($datasetTypes as $id => $datasetType) {
                        $checkedHtml = in_array($id, $model->types) ? 'checked="checked"' : '';
                        echo '<input type="checkbox" name="datasettypes[]" value="'.$id.'"' . $checkedHtml . '/> ' . $datasetType . '<br/>';
                    }
                    ?>
                </div>
            </div>

            <div class="control-group">
                <?php echo $form->labelEx($model, 'title', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="This should be a short descriptive title
                   of the dataset to be submitted"></a>
                <div class="controls">
                    <?php echo $form->textField($model, 'title', array('size' => 60, 'maxlength' => 300)); ?>
                    <?php echo $form->error($model, 'title'); ?>
                </div>
            </div>

            <div class="control-group">

                <?php echo CHtml::label('Estimated Dataset Size', '', array('class' => 'control-label'));
                ?>
                <a class="myHint" data-content="The approximate
                   combined size of all the files that you intend to submit"></a>
                   <?php //echo $form->labelEx($model, 'dataset_size', array('class' => 'control-label'));
                   ?>
                <div class="controls">
                    <?php echo $form->textField($model, 'dataset_size', array('size' => 60, 'maxlength' => 200));
                          echo CHtml::activeDropDownList($model,'union', array('B'=>'Bytes','M'=>'MB','G'=>'GB','T'=>'TB'));?>
                    <?php echo $form->error($model, 'dataset_size'); ?>
                </div>
            </div>
        </div>

        <?php echo $form->hiddenField($image, 'location', array('size' => 60, 'maxlength' => 200, 'readonly' => "readonly", 'class' => 'image')); ?>

        <div class="span6">
            <div class="control-group">
                <font class="control-label">No image</font>
                <a class="myHint" data-content="check it if you don't want to upload an image"></a>
                <div class="controls">
                    <?php echo $form->checkBox($image,'is_no_image', array('id'=>'image-upload')); ?>
                    <!--input id="image-upload" type="checkbox" name="Images[is_no_image]"
                           style="margin-right:5px"/-->
                </div>
            </div>

            <div class="control-group">
                <label class="control-label">Image Upload</label>
                <a class="myHint" data-content="upload an image from your local computer/network"></a>
                <div class="controls">
                    <?php echo $form->fileField($image, 'image_upload', array('class'=>'image')); ?>
                    <?php echo $form->error($image, 'image_upload'); ?>
                </div>
            </div>

            <div class="control-group">
                <?php echo $form->labelEx($image, 'source', array('class' => 'control-label')); ?>
                <a class="myHint" data-content= "from where did you get the image, e.g. wikipedia"></a>
                <div class="controls">
                    <?php echo $form->textField($image, 'source', array('size' => 60, 'maxlength' => 200, 'class' => 'image')); ?>
                    <?php echo $form->error($image, 'source'); ?>
                </div>
            </div>

            <div class="control-group">
                <?php echo $form->labelEx($image, 'tag', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="A brief descriptive title of the image, 
                   this will be shown to users if they hover over the image."></a>
                <div class="controls">
                    <?php echo $form->textField($image, 'tag', array('size' => 60, 'maxlength' => 200, 'class' => 'image')); ?>
                    <?php echo $form->error($image, 'tag'); ?>
                </div>
            </div>

            <div class="control-group">
                <?php echo $form->labelEx($image, 'license', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="GigaScience database will
                   only use images that are free for others to re-use,
                   primarily this is Creative Commons 0 license (CC0)
                   please see <a target='_blank' href='http://creativecommons.org/about/cc0'>here</a> 
                   for further reading on creative commons licenses."></a>
                <div class="controls">
                    <?php echo $form->textField($image, 'license', array('size' => 60, 'maxlength' => 200, 'class' => 'image')); ?>
                    <?php echo $form->error($image, 'license'); ?>
                </div>
            </div>

            <div class="control-group">
                <?php echo $form->labelEx($image, 'photographer', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="The person(s) that should 
                   be credited for the image"></a>
                <div class="controls">
                    <?php echo $form->textField($image, 'photographer', array('size' => 60, 'maxlength' => 200, 'class' => 'image')); ?>
                    <?php echo $form->error($image, 'photographer'); ?>
                </div>
            </div>
        </div>


        <div class="span10">
            <div class="control-group">
                <?php echo $form->labelEx($model, 'description', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="Please provide a full description of the datatset, this may 
                   look like an article abstract giving a brief background of the research and a 
                   description of the results to be found in the dataset
                   (it should be between 100 and 500 word in length). 
                   Please note this text box accepts HTML code tags for formatting,
                   so you may use &quot;&lt; br &gt;&quot; for line breaks, &quot;&lt; em &gt;&QUOT; <em>for italics</em> &quot;
                   &lt; em /&gt;&quot; 
                   and &quot;&lt; b &gt;&quot; <b>for bold</b> &quot;&lt; b/ &gt;&quot;"></a>
                <div class="controls">
                    <?php echo $form->textArea($model, 'description', array('rows' => 6, 'cols' => 100, 'style' => 'resize:vertical;width:610px')); ?>
                    <?php echo $form->error($model, 'description'); ?>
                </div>
            </div>
        </div>
    </div>

    <div class="span12" style="text-align:center">
        <a href="<?= Yii::app()->createUrl('/user/view_profile') ?>" class="btn"/>Cancel</a>
        <?php echo CHtml::submitButton('Next', array('class' => 'btn-green', 'id' => 'next-btn')); ?>
    </div>

</div>

<script>
    $('.date').datepicker();


    $(".next1").click(function() {
        $("#next-btn").click();
    });

    $(".myHint").popover();

    $(".myHintLink").popover({trigger: 'manual'}).hover(function(e) {
        $(this).popover('show');
        e.preventDefault();
    });


    $('.myHintLink').on('mouseleave', function() {
        var v = $(this);
        setTimeout(
                function() {
                    v.popover('hide');
                }, 2000);
    });

    $(function() {
        $('#image-upload').click(function() {
            if ($(this).is(':checked')) {
                $('.image').attr('disabled', true);
            } else {
                $('.image').attr('disabled', false);
            }
        });
    });
    
    function disableImage(){     
//        alert('here');
         if ($('#image-upload').is(':checked')) {
                $('.image').attr('disabled', true);
         }
    }
        
    window.onload = disableImage;

</script>

