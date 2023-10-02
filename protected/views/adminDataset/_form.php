<?
if (Yii::app()->user->hasFlash('saveSuccess'))
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

<?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'dataset-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'enctype' => 'multipart/form-data',
        'class' => 'form-horizontal admindataset-form'
    ),
));

echo $form->hiddenField($model, "image_id");

?>
<div class="col-xs-12 form well">
    <div>
        <p class="note">Fields with <span class="required">*</span> are required.</p>
        <?php if($model->hasErrors()): ?>
            <div class="alert alert-danger" role="alert">
                <?php echo $form->errorSummary($model); ?>
            </div>
        <?php endif; ?>


        <!--TODO: Adding 'style'=>'margin-top:*' to each div is just a temp styling fix, need further investigation on how to implement CSS styling properly.-->

        <div class="form-container">
            <div class="row">
                <!-- first column -->
                <div class="col-xs-5">
                    <div class="form-block-1">
                        <div class="form-group">
                            <?php echo $form->labelEx($model, 'submitter_id', array('class' => 'control-label col-xs-3')); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList(
                                    $model,
                                    'submitter_id',
                                    CHtml::listData(
                                        User::model()->findAll(
                                            array('order' => 'email ASC')
                                        ),
                                        'id',
                                        'email'
                                    ),
                                    array('class' => 'form-control')
                                );
                                ?>
                                <?php echo $form->error($model, 'submitter_id'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model, 'curator_id', array('class' => 'control-label col-xs-3')); ?>
                            <div class="col-xs-9">
                                <?php
                                $criteria = new CDbCriteria;
                                $criteria->condition = 'role=\'admin\' and email like \'%gigasciencejournal.com\'';
                                ?>
                                <?php echo $form->dropDownList($model, 'curator_id', CHtml::listData(User::model()->findAll($criteria), 'id', 'email'), array('prompt' => '', 'class' => 'form-control')); ?>
                                <?php echo $form->error($model, 'curator_id'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model, 'manuscript_id', array('class' => 'control-label col-xs-3')); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model, 'manuscript_id', array('size' => 60, 'maxlength' => 200, 'class' => 'form-control')); ?>
                                <?php echo $form->error($model, 'manuscript_id'); ?>
                            </div>
                        </div>
                        <div class="form-group">
                            <?php echo $form->labelEx($model, 'upload_status', array('class' => 'control-label col-xs-3')); ?>
                            <div class="col-xs-9">
                                <?php echo $form->dropDownList(
                                    $model,
                                    'upload_status',
                                    Dataset::$availableStatusList,
                                    array('class' => 'js-pub form-control', 'disabled' => $model->upload_status == 'Published',)
                                ); ?>
                                <?php echo $form->error($model, 'upload_status'); ?>
                            </div>
                        </div>
                    </div>

                    <div class="form-block-2">
                        <fieldset>
                            <legend>Types</legend>
                            <!-- checkboxes -->
                            <div class="checkbox-group">
                                <?php
                                $datasetTypes = CHtml::listData(Type::model()->findAll(), 'id', 'name');
                                $checkedTypes = CHtml::listData($model->datasetTypes, 'id', 'id');

                                foreach ($datasetTypes as $id => $datasetType) {
                                ?>
                                    <div class="form-checkbox-container row">
                                        <?php
                                        $checkedHtml = in_array($id, $checkedTypes, true) ? 'checked="checked"' : '';
                                        $checkboxId = "Dataset_$datasetType";
                                        ?>
                                        <div class="checkbox-wrapper col-xs-offset-1">
                                        <?php
                                        echo '<input class="" id="' . $checkboxId . '" type="checkbox" name="datasettypes[' . $id . ']" value="1"' . $checkedHtml . '/>';
                                        ?>
                                        </div>
                                        <?php
                                            echo $form->labelEx($model, "$datasetType", array('class' => 'control-label checkbox-label'));
                                        ?>
                                    </div>
                                <?php
                                }
                                ?>
                            </div>
                        </fieldset>
                    </div>

            </div>
            <!-- second column -->
            <div class="col-xs-offset-1 col-xs-5">
                <div id="imageFields" class="form-block-3">
                    <div class="form-group">
                        <div id="imagePreviewWrapper" class="image-preview-wrapper">

                            <?php

                            if ($model->image && 0 !== $model->image_id && $model->image->isUrlValid()) {

                                echo CHtml::ajaxButton(
                                    '[x]',
                                    Yii::app()->createUrl('/adminDataset/clearImageFile/'),
                                    array(
                                        'type' => 'POST',
                                        'data' => array('doi' => 'js:$("#Dataset_identifier").val()'),
                                        'dataType' => 'json',
                                        'success' => 'js:function(output){
                                                    console.log(output);
                                                    if(output.status){
                                                        $("#showImage").src = "";
                                                        $("#showImage").css("display", "none");
                                                        $("#clearFileUrl").css("display", "none");
                                                        window.location.reload();
                                                    }else {
                                                        $("#removing").html("Failed clearing image file url");
                                                    }
                                                }',
                                    ),
                                    array(
                                        'id' => 'clearFileUrl',
                                        'title' => 'Delete file',
                                        'confirm' => 'Are you sure? This will take effect immediately',
                                    )
                                );
                            }

                            if ($model->image) {
                                echo CHtml::image($model->image->url, $model->image->isUrlValid() ? $model->image->tag : "", array('id' => 'showImage'));
                            }
                            echo CHtml::image("", "", array('id' => 'imagePreview'));
                            ?>
                        </div>
                        <label for="image_upload_image" class="control-label col-xs-3">Image Status</label>
                        <?php if ($model->image && 0 != $model->image->id) { ?>
                            <div>
                                <ul>
                                    <li><?php echo CHtml::fileField('datasetImage'); ?></li>
                                    <li><?php echo CHtml::ajaxLink(
                                            'Remove image record (file+metadata)',
                                            Yii::app()->createUrl('/adminDataset/removeImage/'),
                                            array(
                                                'type' => 'POST',
                                                'data' => array('doi' => 'js:$("#Dataset_identifier").val()'),
                                                'dataType' => 'json',
                                                'success' => 'js:function(output){
                                                    console.log(output);
                                                    if(output.status){
                                                        $("#showImage").src = "https://assets.gigadb-cdn.net/images/datasets/no_image.png";
                                                        $(".meta-fields").css("display", "none");
                                                        $("#showImage").css("display", "none");
                                                        $("#removeButton").css("display", "none");
                                                        window.location.reload();
                                                    }else {
                                                        $("#removing").html("Failed removing image");
                                                    }
                                                }',
                                            ),
                                            array(
                                                // 'class' => 'btn btn-sm',
                                                'id' => 'removeButton',
                                                // 'style' => 'width:90%;font-size: smaller; font-weight: lighter;color: #fff ; background: #c12e2a',
                                                'title' => 'the dataset will be associated with the generic image record afterward',
                                                'confirm' => 'Are you sure? This will take effect immediately',

                                            )
                                        );
                                        ?></li>
                                    <li>
                                        <div id="removing"></div>
                                    </li>
                                </ul>
                            </div>
                        <?php } else { ?>
                            <div class='col-xs-9'>
                                <?php echo CHtml::fileField('datasetImage','',array('class'=>'form-control')); ?>
                            </div>
                        <?php } ?>
                    </div>
                    <div class="meta-fields-container">
                        <div class="form-group">
                            <?php echo $form->labelEx($model->image, 'url', array(
                                'class' => 'control-label col-xs-3 meta-fields',
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model->image, 'url', array(
                                    'class' => 'meta-fields form-control',
                                )); ?>
                                <?php echo $form->error($model->image, 'url'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo $form->labelEx($model->image, 'source', array(
                                'class' => 'control-label col-xs-3 meta-fields',
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model->image, 'source', array(
                                    'class' => 'meta-fields form-control',
                                )); ?>
                                <?php echo $form->error($model->image, 'source'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo $form->labelEx($model->image, 'tag', array(
                                'class' => 'control-label col-xs-3 meta-fields',
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model->image, 'tag', array(
                                    'class' => 'meta-fields form-control',
                                )); ?>
                                <?php echo $form->error($model->image, 'tag'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo $form->labelEx($model->image, 'license', array(
                                'class' => 'control-label col-xs-3 meta-fields',
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model->image, 'license', array(
                                    'class' => 'meta-fields form-control',
                                )); ?>
                                <?php echo $form->error($model->image, 'license'); ?>
                            </div>
                        </div>

                        <div class="form-group">
                            <?php echo $form->labelEx($model->image, 'photographer', array(
                                'class' => 'control-label col-xs-3 meta-fields',
                            )); ?>
                            <div class="col-xs-9">
                                <?php echo $form->textField($model->image, 'photographer', array(
                                    'class' => 'meta-fields form-control',
                                )); ?>
                                <?php echo $form->error($model->image, 'photographer'); ?>
                            </div>
                        </div>
                    </div>
                </div>
                <hr />
                <div class="form-block-4">
                    <fieldset aria-labelledby="doiLabel">
                        <div class="form-group row">
                            <?php echo $form->labelEx($model, 'identifier', array(
                                'class'=>'control-label col-xs-3',
                                'id'=>'doiLabel'
                            )); ?>
                            <div class="col-xs-7">
                                <?php echo $form->textField(
                                    $model,
                                    'identifier',
                                    array(
                                        'size' => 32,
                                        'maxlength' => 32,
                                        'disabled' => $model->upload_status == 'Published',
                                        'class'=>'form-control',
                                        'ajax' => array(
                                            'type' => 'POST',
                                            'url' => array('adminDataset/checkDOIExist'),
                                            'dataType' => 'JSON',
                                            'data' => array('doi' => 'js:$(this).val()'),
                                            'success' => 'function(data){
                                                    if(data.status){
                                                        $("#Dataset_identifier").addClass("error");
                                                    }else {
                                                        $("#Dataset_identifier").removeClass("error");

                                                    }
                                                }',
                                        ),
                                    ),
                                ); ?>
                                <?php echo $form->error($model, 'identifier'); ?>
                            </div>
                            <div class="col-xs-2">
                                <?php
                                $status_array = array('Submitted', 'UserStartedIncomplete', 'Curation');
                                echo CHtml::ajaxLink(
                                    'Mint DOI',
                                    Yii::app()->createUrl('/adminDataset/mint/'),
                                    array(
                                        'type' => 'POST',
                                        'data' => array('doi' => 'js:$("#Dataset_identifier").val()'),
                                        'dataType' => 'json',
                                        'success' => 'js:function(output){
                                            console.log(output);
                                            if(output.status){
                                                $("#minting").html("new DOI successfully minted");

                                            }else {
                                                $("#minting").html("error minting a DOI: "+ output.md_curl_status + ", " + output.doi_curl_status);
                                            }
                                            $("#mint_doi_button").toggleClass("active");
                                        }',
                                    ),
                                    array(
                                        'class' => 'btn background-btn control-btn',
                                        'id' => 'mint_doi_button',
                                        'disabled' => in_array($model->upload_status, $status_array),
                                    )
                                );

                                ?>

    <?php
                                if ("Curation" === $model->upload_status) {
                                    echo CHtml::link(
                                        "Move files to public ftp",
                                        "/adminDataset/moveFiles/doi/{$model->identifier}",
                                    );
                                }
                                ?>
                            </div>
                            <div id="minting" class="col-xs-offset-3 col-xs-9 control-msg"></div>
                        </div>

                    </fieldset>

                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'ftp_site', array('class'=>'control-label col-xs-3')); ?>
                        <div class="col-xs-9">
                            <?php echo $form->textField($model, 'ftp_site', array('class' => 'form-control', 'size' => 60, 'maxlength' => 200, 'disabled' => $model->upload_status == 'Published',)); ?>
                            <?php echo $form->error($model, 'ftp_site'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'fairnuse', array('class'=>'control-label col-xs-3')); ?>
                        <div class="col-xs-9">
                            <?php echo $form->textField($model, 'fairnuse', array('class' => 'form-control date',)); ?>
                            <?php echo $form->error($model, 'fairnuse'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'publication_date', array('class'=>'control-label col-xs-3')); ?>
                        <div class="col-xs-9">
                            <?php echo $form->textField($model, 'publication_date', array('class' => 'form-control date js-date-pub', 'disabled' => $model->upload_status == 'Published',)); ?>
                            <?php echo $form->error($model, 'publication_date'); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo $form->labelEx($model, 'modification_date', array('class'=>'control-label col-xs-3')); ?>
                        <div class="col-xs-9">
                            <?php echo $form->textField($model, 'modification_date', array('class' => 'form-control date',)); ?>
                            <?php echo $form->error($model, 'modification_date'); ?>
                        </div>
                    </div>
                </div>

            </div>

        </div> <!-- end of row of two columns -->

        <hr />

        <div class="row form-block-5">

            <div class="col-xs-12">
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'dataset_size', array('label' => 'Dataset Size in Bytes','class'=>'control-label col-xs-3')); ?>
                    <div class='col-xs-7'>
                        <?php echo $form->textField($model, 'dataset_size', array('class' => 'form-control', 'size' => 60, 'maxlength' => 300,)); ?>
                        <?php echo $form->error($model, 'dataset_size'); ?>
                    </div>
                </div>
                <div class="form-group">
                    <?php echo $form->labelEx($model, 'title', array('class'=>'control-label col-xs-3')); ?>
                    <div class='col-xs-7'>
                        <?php echo $form->textField($model, 'title', array('class' => 'form-control', 'size' => 60, 'maxlength' => 300,)); ?>
                        <?php echo $form->error($model, 'title'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <?php echo $form->labelEx($model, 'description', array('class'=>'control-label col-xs-3')); ?>
                    <div class='col-xs-7'>
                        <?php echo $form->textArea($model, 'description', array('class' => 'form-control textarea-control', 'rows' => 8, 'cols' => 50,)); ?>
                        <?php echo $form->error($model, 'description'); ?>
                    </div>
                </div>

                <div class="form-group">
                    <?php echo CHtml::label('Keywords', 'keywords', array('class'=>'control-label col-xs-3')); ?>
                    <div class='col-xs-7'>
                        <!-- NOTE this input is repositioned outside the viewport by teh tagEditor plugin but is still focusable, so the keyboard navigation is very confusing. Fixing this is not trivial, so warrants another ticket #1467 -->
                        <?php echo CHtml::textField('keywords', '', array('class' => 'form-control', 'size' => 60, 'maxlength' => 300)); ?>
                    </div>
                </div>

                <div class="form-group">
                    <?php echo CHtml::label('URL to redirect', 'urltoredirect', array('class'=>'control-label col-xs-3')); ?>
                    <div class='col-xs-7'>
                        <?php echo CHtml::textField('urltoredirect', $model->getUrlToRedirectAttribute(), array('class' => 'form-control', 'size' => 60, 'maxlength' => 300,)); ?>
                    </div>
                </div>
            </div>
        </div> <!-- end of row of one column -->

        <!-- <?php echo CHtml::link('Curation Log', $this->createAbsoluteUrl('curationlog/admin', array('id' => $model->id))); ?> -->

        <?php if (isset($dataset_id)) {
            ?>
                <hr />
                <div class="form-block-6">
                    <?php
                    echo $this->renderPartial("curationLog", array('dataset_id' => $dataset_id, 'model' => $curationlog));
                    ?>
                </div>
            <?php
            }
            ?>

    </div> <!-- end of container -->

</div>
</div>

<div class="col-xs-12 form-control-btns">
    <a class="btn background-btn-o" href="<?= Yii::app()->createUrl('/adminDataset/admin') ?>" />Cancel and go back</a>
    <?= CHtml::submitButton(
        $model->isNewRecord ? 'Create' : 'Save',
        array('class' => 'btn background-btn submit-btn')
    ); ?>
    <?php if ("hidden" === $datasetPageSettings->getPageType() || "draft" === $datasetPageSettings->getPageType()) { ?>
        <a href="<?= Yii::app()->createUrl('/adminDataset/private/identifier/' . $model->identifier) ?>" />Create/Reset Private URL</a>
        <?php if ($model->token) { ?>
            <a href="<?= Yii::app()->createUrl('/dataset/' . $model->identifier . '/token/' . $model->token) ?>">Open Private URL</a>
        <?php } ?>
    <?php } elseif ("mockup" === $datasetPageSettings->getPageType()) {
        echo CHtml::link('Generate mockup for reviewers', '#', array(
            'class' => 'btn background-btn',
            'data-toggle' => "modal", 'data-target' => "#mockupCreation"
        ));
    }
    ?>

</div>
<?php $this->endWidget(); ?>
<div class="modal fade" id="mockupCreation" tabindex="-1" role="dialog" aria-labelledby="generateMockup">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                <h2 class="modal-title">Generate unique and time-limited mockup url for reviewers</h2>
            </div>
            <?php echo CHtml::beginForm("/adminDataset/mockup/id/" . $model->id, "POST", ["id" => "mockupform"]); ?>
            <div class="modal-body">
                <label for="reviewerEmail">Reviewer's email</label>
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
                <?php echo CHtml::submitButton("Generate mockup", ["class" => "btn-green mockup"]); ?>
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
        $('.date').datepicker({
            'dateFormat': 'yy-mm-dd'
        });

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
    echo "var existingTags = " . $js_array . ";\n";
    ?>
    $('#keywords').tagEditor({
        initialTags: existingTags,
        delimiter: ',',
        /* comma */
        placeholder: 'Enter keywords (separated by commas) ...'
    });

    $(function() {
        $('#mint_doi_button').click(function() {
            $('#minting').html('minting under way, please wait');
            $(this).toggleClass('active');
        });
    });

    var image = document.getElementById("showImage");
    var image_id = document.getElementById("Dataset_image_id").value;
    const metaFields = $('.meta-fields');
    const metaFieldsContainer = $('.meta-fields-container');

    //Show image meta data, preview uploaded image in update page
    if (image.src != 'https://assets.gigadb-cdn.net/images/datasets/no_image.png') {
        metaFields.css('display', '');
        const imgPrevWrapper = $('#imagePreviewWrapper')
        imgPrevWrapper.css('display', 'none');
        document.getElementById("datasetImage").addEventListener('change', (event) => {
            const preview = document.getElementById("imagePreview");
            if (event.target.files.length != 0) {
                var src = URL.createObjectURL(event.target.files[0]);
                preview.src = src;
                imgPrevWrapper.css('display', 'block');
                metaFields.css('display', '');
                $('#showImage').css('display', 'none');
                $('#removeButton').css('display', 'none');
            } else {
                metaFields.css('display', '');
                $('#showImage').css('display', 'block');
                $('#removeButton').css('display', '');
                imgPrevWrapper.css('display', 'none');
                preview.style.display = "none";
            }
        })
    };

    //Show image meta data, preview uploaded image in create page

    document.getElementById("datasetImage").addEventListener('change', (event) => {
        if (event.target.files.length != 0) {
            var src = URL.createObjectURL(event.target.files[0]);
            var preview = document.getElementById("imagePreview");
            preview.src = src;
            preview.style.display = "block";
            metaFieldsContainer.removeClass('hidden');
            $('#showImage').css('display', 'none');
            console.log('event.target.files.length != 0')
        } else {
            $('#showImage').css('display', 'block');
            $('#imagePreview').css('display', 'none');
            metaFieldsContainer.addClass('hidden');
            console.log('event.target.files.length == 0')
        }
    });

    // if no image loaded and no image selected for upload, don't show metadata fields (unless there is a custom image associated with the dataset)
    if ('' == image.src && 0 == document.getElementById("datasetImage").files.length) {

        if (0 == image_id || null == image_id) {
            metaFieldsContainer.addClass('hidden');
            console.log('0 == image_id || null == image_id')
        }
    }
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