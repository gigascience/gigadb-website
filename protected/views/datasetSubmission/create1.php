<?
if (Yii::app()->user->hasFlash('saveSuccess'))
    echo Yii::app()->user->getFlash('saveSuccess');

$cs = Yii::app()->getClientScript();
$cssCoreUrl = $cs->getCoreScriptUrl();
$cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
$cs->registerCssFile('/css/jquery.tag-editor.css');


Yii::app()->clientScript->registerScriptFile(Yii::app()->baseUrl . '/js/ckeditor/ckeditor.js', CClientScript::POS_BEGIN);
?>
<script src="https://cdnjs.cloudflare.com/ajax/libs/caret/1.0.0/jquery.caret.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/tag-editor/1.0.20/jquery.tag-editor.min.js"></script>
<script>
    /**
     * Copyright (c) 2003-2019, CKSource - Frederico Knabben. All rights reserved.
     * For licensing, see LICENSE.md or https://ckeditor.com/legal/ckeditor-oss-license
     */

    /* exported initSample */

    if ( CKEDITOR.env.ie && CKEDITOR.env.version < 9 )
        CKEDITOR.tools.enableHtml5Elements( document );

    // The trick to keep the editor in the sample quite small
    // unless user specified own height.
    CKEDITOR.config.height = 150;
    CKEDITOR.config.width = 'auto';

    var initSample = ( function() {
        var wysiwygareaAvailable = isWysiwygareaAvailable(),
            isBBCodeBuiltIn = !!CKEDITOR.plugins.get( 'bbcode' );

        return function() {
            var editorElement = CKEDITOR.document.getById( 'Dataset_description' );

            // :(((
            if ( isBBCodeBuiltIn ) {
                editorElement.setHtml(
                    'Hello world!\n\n' +
                    'I\'m an instance of [url=https://ckeditor.com]CKEditor[/url].'
                );
            }

            // Depending on the wysiwygarea plugin availability initialize classic or inline editor.
            if ( wysiwygareaAvailable ) {
                CKEDITOR.replace( 'Dataset_description' );
            } else {
                editorElement.setAttribute( 'contenteditable', 'true' );
                CKEDITOR.inline( 'Dataset_description' );

                // TODO we can consider displaying some info box that
                // without wysiwygarea the classic editor may not work.
            }
        };

        function isWysiwygareaAvailable() {
            // If in development mode, then the wysiwygarea must be available.
            // Split REV into two strings so builder does not replace it :D.
            if ( CKEDITOR.revision == ( '%RE' + 'V%' ) ) {
                return true;
            }

            return !!CKEDITOR.plugins.get( 'wysiwygarea' );
        }
    } )();
</script>

<h2><?= $model->getIsNewRecord() ? 'Create Dataset' : 'Manage Your Dataset' ?></h2>
<div class="clear"></div>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'dataset-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'),
));
?>

<?php $this->renderPartial('_tabs_navigation', array('model' => $model)); ?>

<div class="span12 form well">
    <div class="form-horizontal">
        <p class="note">Fields with <span class="required">*</span> are required.</p>
        <div class="clear"></div>
        <div class="span10">
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
                <?php echo $form->labelEx($model, 'manuscript_id', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="If you have already submitted your manuscript to GigaScience journal please provide the submission number here so that we can easily link these data to the manuscript. A manuscript submission number will look something like GIGA-D-19-012345"></a>
                <div class="controls">
                    <?php echo $form->textField($model, 'manuscript_id', array('size' => 60, 'maxlength' => 50, 'placeholder' => 'e.g. GIGA-D-18-00123')); ?>
                    <?php echo $form->error($model, 'manuscript_id'); ?>
                </div>
            </div>
        </div>

        <div class="span5">
            <div class="control-group">
                <?php echo $form->labelEx($model, 'types', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="Select the type of data to be included
                   in this submission, you may select more than 1. If a
                   data type is missing please contact us on database@gigasciencejournal.com."></a>
                <div class="controls">
                    <?
                    $datasetTypes = CHtml::listData(Type::model()->findAll(array('order' => 'name ASC')), 'id', 'name');
                    foreach ($datasetTypes as $id => $datasetType) {
                        $checkedHtml = '';
                        foreach ($model->typeIds as $existDatasetTypeId) {
                            if ($id == $existDatasetTypeId) {
                                $checkedHtml = 'checked="checked"';
                                break;
                            }

                        }
                        echo '<input type="checkbox" name="datasettypes[]" value="'.$id.'"' . $checkedHtml . '/> ' . $datasetType . '<br/>';
                    }
                    ?>
                </div>
                <?php echo $form->error($model, 'types'); ?>
            </div>
        </div>

        <?php echo $form->hiddenField($image, 'location', array('size' => 60, 'maxlength' => 200, 'readonly' => "readonly", 'class' => 'image')); ?>

        <div class="span6">
            <div class="control-group">
                <label class="control-label">Image Upload</label>
                <a class="myHint" data-content="upload an image from your local computer/network"></a>
                <div class="controls">
                    <?php echo $form->fileField($image, 'image_upload', array('class'=>'image')); ?>
                    <?php echo $form->error($image, 'image_upload'); ?>
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
                <a class="myHint" data-html="true" data-content="GigaScience database will
                   only use images that are free for others to re-use,
                   primarily this is Creative Commons 0 license (CC0)
                   please see <a target='_blank' href='http://creativecommons.org/about/cc0'>here</a>
                   for further reading on creative commons licenses."></a>
                <div class="controls">
                    <?php echo $form->dropDownList($image,'license', array(
                        '' => 'Please select…',
                        'CC0' => 'CC0',
                        'CC BY' => 'CC BY',
                        'Public Domain' => 'Public Domain',
                    ), array('class' => 'image dropdown-white')); ?>
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

            <div class="control-group">
                <?php echo $form->labelEx($image, 'source', array('class' => 'control-label')); ?>
                <a class="myHint" data-content= "from where did you get the image, e.g. wikipedia"></a>
                <div class="controls">
                    <?php echo $form->textField($image, 'source', array('size' => 60, 'maxlength' => 200, 'class' => 'image')); ?>
                    <?php echo $form->error($image, 'source'); ?>
                </div>
            </div>

            <div class="control-group">
                <font class="control-label">OR</font>
            </div>

            <div class="control-group">
                <font class="control-label" style="width: 340px">If you are unable to provide a suitable image to help your dataset be more distinguished you may tick this box to use a generic GigaDB logo instead.</font>
                <div class="controls">
                    <?php echo $form->checkBox($image,'is_no_image', array('id'=>'image-upload', 'style' => 'margin: 25px 0 0 15px;')); ?>
                    <!--input id="image-upload" type="checkbox" name="Images[is_no_image]"
                           style="margin-right:5px"/-->
                </div>
            </div>
        </div>


        <div class="span10">
            <div class="control-group">
                <?php echo $form->labelEx($model, 'title', array('class' => 'control-label')); ?>
                <a class="myHint" data-content="This should be a short descriptive title
                   of the dataset to be submitted"></a>
                <div class="controls">
                    <?php echo $form->textField($model, 'title', array('size' => 60, 'maxlength' => 300, 'style' => 'resize:vertical;width:610px', 'placeholder' => 'e.g. Supporting data for “GigaScience manuscript  title”')); ?>
                    <div id="title-warning" class="hide" style="color: red">Warning: Your title is over 100 characters long, you should reduce it if possible.</div>
                    <?php echo $form->error($model, 'title'); ?>
                    <div>Note – The title should be unique to the dataset and must be different to any associated manuscript. This can be achieved by prefixing the manuscript title with “Supporting data for” if you wish.</div>
                </div>
            </div>

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
        <div class="span10">
            <div class="control-group">
                <?php echo CHtml::label('Keywords','keywords', array('class'=>'control-label')); ?>
                <div class="controls">
                    <?php echo CHtml::textField('keywords', '', array('class'=>'span10', 'size'=>60,'maxlength'=>300)); ?>
                    <div>Note – All keywords are lowcase</div>
                </div>
            </div>

            <?php $class = ''; ?>
            <?php $disabled = false; ?>
            <?php if ($model->getIsNewRecord()): ?>
                <?php $class = ' upload-control' ?>
                <?php $disabled = true; ?>
                <div class="control-group">
                    <div class="controls">
                        <input id="agree-checkbox" type="checkbox" style="margin-right:5px"/><a target="_blank" href="/site/term">I have read GigaDB's Terms and Conditions</a>
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <div class="span12" style="text-align:center">
        <a href="<?= Yii::app()->createUrl('/datasetSubmission/choose') ?>" class="btn"/>Cancel</a>
        <?php echo CHtml::submitButton('Save', array(
            'class' => 'btn-green' . $class,
            'id' => 'next-btn', 'disabled' => $disabled
        ));
        ?>
        <?php if (!$model->getIsNewRecord()): ?>
            <?php echo CHtml::submitButton('Next', array(
                'class' => 'btn-green' . $class,
                'id' => 'next-btn2', 'disabled' => $disabled,
                'data-url' => '/datasetSubmission/authorManagement/id/' . $model->id
            ));
            ?>
        <?php endif ?>
        <input type="hidden" name="redirect_url" id="redirect_url" value="">
    </div>

</div>

<?php $this->endWidget(); ?>

<script>
    $('#next-btn2').click(function () {
        $('#redirect_url').val($(this).data('url'));
    });

    $('.date').datepicker();

    $(function() {
        $('#image-upload').click(function() {
            if ($(this).is(':checked')) {
                $('.image').attr('disabled', true);
            } else {
                $('.image').attr('disabled', false);
            }
        });

        $('#agree-checkbox').click(function() {
            if ($(this).is(':checked')) {
                $('.upload-control').attr('disabled', false);
            } else {
                $('.upload-control').attr('disabled', true);
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

    //warning for title
    $("#Dataset_title").on('change', function () {
        var title = $(this);
        var titleWarning = $('#title-warning');

        if (title.val().length > 100) {
            titleWarning.show();
        } else {
            titleWarning.hide();
        }
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
</script>
<script>
    initSample();
</script>
