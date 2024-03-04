<?php if ($flashSuccess = Yii::app()->user->getFlash('updateSuccess')) { ?>
    <div class="alert alert-success" role="alert">
        <?= $flashSuccess ?>
    </div>
<?php } ?>

<?php if ($flashError = Yii::app()->user->getFlash('updateError')) { ?>
    <div class="alert alert-danger" role="alert">
        <?= $flashError ?>
    </div>
<?php } ?>

<?php
$cs = Yii::app()->getClientScript();
$cssCoreUrl = $cs->getCoreScriptUrl();

$cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
$cs->registerCssFile('/css/jquery.tag-editor.css');
?>

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
        <div role="alert">
            <?php if ($model->hasErrors()) : ?>
                <div class="alert alert-danger">
                    <?php echo $form->errorSummary($model); ?>
                </div>
            <?php endif; ?>
        </div>

        <div>
            <div class="row">
                <!-- first column -->
                <div class="col-xs-5">
                    <div class="form-block-1">
                      <?php
                        $this->widget('application.components.controls.DropdownField', [
                          'form' => $form,
                          'model' => $model,
                          'attributeName' => 'submitter_id',
                          'listDataOptions' => [
                              'data' => User::model()->findAll(
                                  array('order' => 'email ASC')
                              ),
                              'valueField' => 'id',
                              'textField' => 'email',
                          ],
                          'labelOptions' => ['class' => 'col-xs-4'],
                          'inputWrapperOptions' => 'col-xs-8',
                          'inputOptions' => [
                              'required' => true,
                          ]
                        ]);
                        ?>
                        <div class="form-group <?php echo $form->error($model, 'curator_id') ? 'has-error' : '' ?>">
                            <?php echo $form->labelEx($model, 'curator_id', array('class' => 'control-label col-xs-4')); ?>
                            <div class="col-xs-8">
                                <?php
                                $criteria = new CDbCriteria;
                                $criteria->condition = 'role=\'admin\' and email like \'%gigasciencejournal.com\'';
                                ?>
                                <?php echo $form->dropDownList($model, 'curator_id', CHtml::listData(User::model()->findAll($criteria), 'id', 'email'), array('prompt' => '', 'class' => 'form-control')); ?>
                                <div role="alert" class="help-block">
                                  <?php echo $form->error($model, 'curator_id'); ?>
                                </div>
                            </div>
                        </div>

                        <?php

                        $this->widget('application.components.controls.TextField', [
                          'form' => $form,
                          'model' => $model,
                          'attributeName' => 'manuscript_id',
                          'labelOptions' => ['class' => 'col-xs-4'],
                          'inputWrapperOptions' => 'col-xs-8',
                          'inputOptions' => [
                            'maxlength' => 200
                          ],
                        ]);
                        ?>

                        <div class="form-group <?php echo $form->error($model, 'upload_status') ? 'has-error' : '' ?>">
                            <?php echo $form->labelEx($model, 'upload_status', array('class' => 'control-label col-xs-4')); ?>
                            <div class="col-xs-8">
                                <?php echo $form->dropDownList(
                                    $model,
                                    'upload_status',
                                    Dataset::getAvailableStatusList(),
                                    array('class' => 'js-pub form-control', 'disabled' => $model->upload_status == 'Published',)
                                ); ?>
                                <div role="alert" class="help-block">
                                <?php echo $form->error($model, 'upload_status'); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="form-block-2">
                        <fieldset>
                            <legend>Dataset Types</legend>
                            <!-- checkboxes -->
                            <div class="checkbox-group">
                                <?php
                                $datasetTypes = CHtml::listData(Type::model()->findAll(), 'id', 'name');
                                $checkedTypes = CHtml::listData($model->datasetTypes, 'id', 'id');

                                foreach ($datasetTypes as $id => $datasetType) {
                                ?>
                                    <div class="from-group checkbox-horizontal">
                                        <?php
                                        $checkedHtml = in_array($id, $checkedTypes, true) ? 'checked="checked"' : '';
                                        $checkboxId = "Dataset_$datasetType";

                                        echo $form->labelEx($model, "$datasetType", array('class' => 'col-md-4 control-label'));
                                        ?>
                                        <div class="col-md-8 col-xs-1">
                                            <?php
                                            echo '<input class="" id="' . $checkboxId . '" type="checkbox" name="datasettypes[' . $id . ']" value="1"' . $checkedHtml . '/>';
                                            ?>
                                        </div>
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
                        <div>
                            <div id="imagePreviewWrapper" class="image-preview-wrapper">

                                <?php
                                if ($model->image) {
                                    echo CHtml::image($model->image->url, $model->image->isUrlValid() ? $model->image->tag : "", array('id' => 'showImage', 'class' => 'dataset-image'));
                                }
                                echo CHtml::image("", "", array('id' => 'imagePreview', 'alt' => ''));

                                if ($model->image && 0 !== $model->image_id && $model->image->isUrlValid()) {

                                    echo CHtml::ajaxButton(
                                        'X',
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
                                                        $("#removing").addClass("block-spacing");
                                                        $("#datasetImage").attr("aria-describedby", "removing");
                                                    }
                                                }',
                                        ),
                                        array(
                                            'id' => 'clearFileUrl',
                                            'class' => 'clear-file-url-btn btn background-btn-o',
                                            'aria-label' => 'Delete image file',
                                            'confirm' => 'Are you sure? This will take effect immediately',
                                        )
                                    );
                                }
                                ?>
                            </div>
                            <?php if ($model->image && 0 != $model->image->id) { ?>
                                <fieldset aria-label="Image upload">
                                    <div class="form-group">
                                        <label for="datasetImage" class="control-label col-xs-4">Image Status</label>
                                        <div class="col-xs-8 block-spacing">
                                            <?php echo CHtml::fileField('datasetImage', '', array('class' => 'form-control', 'aria-controls' => 'metaFieldsSection')); ?>
                                        </div>
                                        <div class="col-xs-offset-4 col-xs-5 block-spacing">
                                            <?php echo CHtml::ajaxLink(
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
                                                        $("#showImage").alt = "Default placeholder image";
                                                        $(".meta-fields").css("display", "none");
                                                        $("#showImage").css("display", "none");
                                                        $("#removeButton").css("display", "none");
                                                        window.location.reload();
                                                    }else {
                                                        $("#removing").html("Failed removing image");
                                                        $("#removing").addClass("block-spacing");
                                                        $("#datasetImage").attr("aria-describedby", "removing");
                                                    }
                                                }',
                                                ),
                                                array(
                                                    'class' => 'btn btn-sm danger-btn',
                                                    'id' => 'removeButton',
                                                    'title' => 'the dataset will be associated with the generic image record afterward',
                                                    'confirm' => 'Are you sure? This will take effect immediately',

                                                )
                                            );
                                            ?>
                                        </div>
                                        <div class="col-xs-offset-4 col-xs-8">
                                            <div id="removing" role="alert"></div>
                                        </div>
                                    </div>
                                </fieldset>
                            <?php } else { ?>
                                <div class="form-group">
                                    <label for="datasetImage" class="control-label col-xs-4">Image Status</label>
                                    <div class='col-xs-8'>
                                        <?php echo CHtml::fileField('datasetImage', '', array('class' => 'form-control', 'aria-controls' => 'metaFieldsSection')); ?>
                                    </div>
                                </div>
                            <?php } ?>
                        </div>
                        <fieldset id="metaFieldsSection" class="meta-fields-container">
                            <legend>Image metafields</legend>
                            <?php
                              $this->widget('application.components.controls.TextField', [
                                'form' => $form,
                                'model' => $model->image,
                                'attributeName' => 'url',
                                'labelOptions' => ['class' => 'col-xs-4'],
                                'inputWrapperOptions' => 'col-xs-8',
                                'inputOptions' => [
                                  'class' => 'meta-fields'
                                ],
                              ]);
                              $this->widget('application.components.controls.TextField', [
                                'form' => $form,
                                'model' => $model->image,
                                'attributeName' => 'source',
                                'labelOptions' => ['class' => 'col-xs-4'],
                                'inputWrapperOptions' => 'col-xs-8',
                                'inputOptions' => [
                                  'required' => true,
                                  'class' => 'meta-fields'
                                ],
                              ]);
                              $this->widget('application.components.controls.TextField', [
                                'form' => $form,
                                'model' => $model->image,
                                'attributeName' => 'tag',
                                'labelOptions' => ['class' => 'col-xs-4'],
                                'inputWrapperOptions' => 'col-xs-8',
                                'inputOptions' => [
                                  'class' => 'meta-fields'
                                ],
                              ]);
                              $this->widget('application.components.controls.TextField', [
                                'form' => $form,
                                'model' => $model->image,
                                'attributeName' => 'license',
                                'labelOptions' => ['class' => 'col-xs-4'],
                                'inputWrapperOptions' => 'col-xs-8',
                                'inputOptions' => [
                                  'required' => true,
                                  'class' => 'meta-fields'
                                ],
                              ]);
                              $this->widget('application.components.controls.TextField', [
                                'form' => $form,
                                'model' => $model->image,
                                'attributeName' => 'photographer',
                                'labelOptions' => ['class' => 'col-xs-4'],
                                'inputWrapperOptions' => 'col-xs-8',
                                'inputOptions' => [
                                  'required' => true,
                                  'class' => 'meta-fields'
                                ],
                              ]);

                              ?>
                        </fieldset>
                        <div id="metaFieldsLiveRegion" aria-live="polite" class="sr-only"></div>
                    </div>
                    <div class="form-block-4">
                        <fieldset>
                            <legend>Dataset metafields</legend>
                            <div class="form-group row <?php echo $form->error($model, 'identifier') ? 'has-error' : ''; ?>" id="doiFormGroup">
                                <?php echo $form->labelEx($model, 'identifier', array(
                                    'class' => 'control-label col-xs-4',
                                    'id' => 'doiLabel'
                                )); ?>
                                <div class="col-xs-6">
                                    <?php echo $form->textField(
                                        $model,
                                        'identifier',
                                        array(
                                            'required' => 'required',
                                            'aria-required' => 'true',
                                            'size' => 32,
                                            'maxlength' => 32,
                                            'disabled' => $model->upload_status == 'Published',
                                            'class' => 'form-control',
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
                                    <div class="help-block" role="alert">
                                    <?php echo $form->error($model, 'identifier'); ?>
                                  </div>
                                </div>
                                <div class="col-xs-2">
                                    <?php
                                    $status_array = array('Submitted', 'UserStartedIncomplete', 'Curation');
                                    echo CHtml::ajaxButton(
                                        'Mint DOI',
                                        Yii::app()->createUrl('/adminDataset/mint/'),
                                        array(
                                            'type' => 'POST',
                                            'data' => array('doi' => 'js:$("#Dataset_identifier").val()'),
                                            'dataType' => 'json',
                                            'success' => 'js:function(output){
                                            console.log(output);
                                            if (output.check_metadata_status == 200 && output.check_doi_status == 200 && output.update_md_status == 201) {
                                                $("#minting").addClass("alert alert-info");
                                                $("#minting").html("This DOI exists in datacite already, no need to mint, but the metadata is updated!");
                                            } else if (output.create_md_status == 201 && output.create_doi_status == 201) {
                                                $("#minting").removeClass("alert alert-success");
                                                $("#minting").html("new DOI successfully minted");
                                            }else {
                                                $("#minting").addClass("alert alert-danger");
                                                $("#minting").html("error minting a DOI: "+ output.create_md_status + ", " + output.create_doi_status);
                                            }
                                            $("#mint_doi_button").toggleClass("active");
                                        }',
                                        ),
                                        array(
                                            'class' => 'btn background-btn m-0',
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
                                <div id="minting" class="col-xs-offset-4 col-xs-8" role="alert"></div>
                            </div>


                        <?php
                          $this->widget('application.components.controls.TextField', [
                            'form' => $form,
                            'model' => $model,
                            'attributeName' => 'ftp_site',
                            'labelOptions' => ['class' => 'col-xs-4'],
                            'inputWrapperOptions' => 'col-xs-8',
                            'inputOptions' => [
                              'required' => true,
                              'maxlength' => 200,
                              'disabled' => $model->upload_status == 'Published'
                            ],
                          ]);
                          $this->widget('application.components.controls.DateField', [
                            'form' => $form,
                            'model' => $model,
                            'attributeName' => 'fairnuse',
                            'labelOptions' => ['class' => 'col-xs-4'],
                            'inputWrapperOptions' => 'col-xs-8',
                          ]);

                          $this->widget('application.components.controls.DateField', [
                            'form' => $form,
                            'model' => $model,
                            'attributeName' => 'publication_date',
                            'labelOptions' => ['class' => 'col-xs-4'],
                            'inputWrapperOptions' => 'col-xs-8',
                            'inputOptions' => [
                              'class' => 'js-date-pub',
                              'disabled' => $model->upload_status == 'Published'
                            ],
                          ]);

                          $this->widget('application.components.controls.DateField', [
                            'form' => $form,
                            'model' => $model,
                            'attributeName' => 'modification_date',
                            'labelOptions' => ['class' => 'col-xs-4'],
                            'inputWrapperOptions' => 'col-xs-8'
                          ]);
                        ?>

                      </fieldset>
                    </div>

                </div>

            </div> <!-- end of row of two columns -->

            <hr />

            <div class="row form-block-5">

                <div class="col-xs-12">

                <?php
                  $this->widget('application.components.controls.TextField', [
                    'form' => $form,
                    'model' => $model,
                    'attributeName' => 'dataset_size',
                    'labelOptions' => ['class' => 'col-xs-2'],
                    'inputWrapperOptions' => 'input-wrapper col-xs-6',
                    'inputOptions' => [
                      'required' => true,
                      'size' => 60,
                      'maxlength' => 300
                    ],
                  ]);
                  $this->widget('application.components.controls.TextField', [
                    'form' => $form,
                    'model' => $model,
                    'attributeName' => 'title',
                    'labelOptions' => ['class' => 'col-xs-4'],
                    'inputWrapperOptions' => 'input-wrapper col-xs-6',
                    'inputOptions' => [
                      'required' => true,
                      'size' => 60,
                      'maxlength' => 300
                    ],
                  ]);
                  $this->widget('application.components.controls.TextArea', [
                    'form' => $form,
                    'model' => $model,
                    'attributeName' => 'description',
                    'labelOptions' => ['class' => 'col-xs-4'],
                    'inputWrapperOptions' => 'input-wrapper col-xs-6',
                    'inputOptions' => [
                        'rows' => 8,
                        'cols' => 50
                    ],
                  ]);
                ?>

                    <div class="form-group">
                        <?php echo CHtml::label('Keywords', 'keywords', array('class' => 'control-label col-xs-4')); ?>
                        <div class='col-xs-6 input-wrapper'>
                            <!-- NOTE this input is repositioned outside the viewport by teh tagEditor plugin but is still focusable, so the keyboard navigation is very confusing. Fixing this is not trivial, so warrants another ticket #1467 -->
                            <?php echo CHtml::textArea('keywords', '', array('class' => 'form-control', 'size' => 60, 'maxlength' => 300)); ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <?php echo CHtml::label('URL to redirect', 'urltoredirect', array('class' => 'control-label col-xs-4')); ?>
                        <div class='col-xs-6 input-wrapper '>
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
    <a class="btn background-btn-o" href="<?= Yii::app()->createUrl('/adminDataset/admin') ?>">Cancel and go back</a>
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
                <h2 class="modal-title" id="generateMockup">Generate unique and time-limited mockup url for reviewers</h2>
            </div>
            <?php echo CHtml::beginForm("/adminDataset/mockup/id/" . $model->id, "POST", ["id" => "mockupform"]); ?>
            <div class="modal-body">
                <label for="reviewerEmail">Reviewer's email</label>
                <input type="text" name="revieweremail" id="reviewerEmail" class="form-control" />
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
        placeholder: 'Enter keywords (separated by commas) ...',
    });

    $(function() {
        $('#mint_doi_button').click(function() {
            $('#minting').html('minting under way, please wait');
            $(this).toggleClass('active');
        });
    });

    var image = document.getElementById("showImage");
    if (image.src.match('images/datasets/no_image.png')) {
      image.alt = "Default placeholder image"
    }
    var image_id = document.getElementById("Dataset_image_id").value;
    const metaFields = $('.meta-fields');
    const metaFieldsContainer = $('.meta-fields-container');
    const metaFieldsLiveRegion = $('#metaFieldsLiveRegion');
    const hiddenText = 'Image metafields are now hidden.'
    const shownText = 'Image metafields are now displayed.'

    // delay is used to prevent screen reader from reading the text too early therefore losing spotlight to page title announcement
    function updateMetaFieldsLiveRegion(text, delay = 1000) {
        setTimeout(() => {
            metaFieldsLiveRegion.text(text);
        }, delay);
    }

    //Show image meta data, preview uploaded image in update page
    const imgPrevWrapper = $('#imagePreviewWrapper')


    if (image.src != 'https://assets.gigadb-cdn.net/images/datasets/no_image.png') {
        metaFields.css('display', '');
        imgPrevWrapper.css('display', 'flex');

        document.getElementById("datasetImage").addEventListener('change', (event) => {
            const preview = document.getElementById("imagePreview");
            if (event.target.files.length != 0) {
                var src = URL.createObjectURL(event.target.files[0]);
                preview.src = src;
                imgPrevWrapper.css('display', 'flex');
                metaFields.css('display', '');
                updateMetaFieldsLiveRegion(shownText);
                $('#showImage').css('display', 'none');
                $('#removeButton').css('display', 'none');
            } else {
                metaFields.css('display', '');
                updateMetaFieldsLiveRegion(hiddenText);
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
            imgPrevWrapper.css('display', 'flex');
            var src = URL.createObjectURL(event.target.files[0]);
            var preview = document.getElementById("imagePreview");
            preview.src = src;
            preview.style.display = "block";
            metaFieldsContainer.show();
            updateMetaFieldsLiveRegion(shownText);
            $('#showImage').css('display', 'none');
        } else {
            $('#showImage').css('display', 'block');
            $('#imagePreview').css('display', 'none');
            metaFieldsContainer.hide();
            updateMetaFieldsLiveRegion(hiddenText);
        }
    });

    // if no image loaded and no image selected for upload, don't show metadata fields (unless there is a custom image associated with the dataset)
    if ('' == image.src && 0 == document.getElementById("datasetImage").files.length) {
        if (0 == image_id || null == image_id) {
            imgPrevWrapper.css('display', 'none');
            metaFieldsContainer.hide();
            metaFieldsLiveRegion.text(hiddenText);
        } else {
            console.log('0 != image_id && null != image_id')
            metaFieldsLiveRegion.text(shownText);
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


<script>
$(document).ready(function() {
  document.addEventListener('focus', function() {
    console.log(document.activeElement);
  })
})
</script>