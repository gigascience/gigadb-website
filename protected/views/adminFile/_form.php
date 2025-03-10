<div class="section form row">

    <div class="col-md-offset-3 col-md-6">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'file-form',
            'enableAjaxValidation' => false,
        )); ?>

        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php if ($model->hasErrors()) : ?>
            <div class="alert alert-danger">
                <?php echo $form->errorSummary($model); ?>
            </div>
        <?php endif; ?>


        <?php
        $this->widget('application.components.controls.DropdownField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'dataset_id',
            'listDataOptions' => [
                'data' => Util::getDois(),
                'valueField' => 'id',
                'textField' => 'identifier',
            ],
            'inputOptions' => [
                'required' => true,
            ],
            'tooltip' => 'The DOI of the dataset to which the file is linked, select from dropdown menu'
        ]);
        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'name',
            'inputOptions' => [
                'required' => true,
                'maxlength' => 100
            ],
            'tooltip' => 'the name of the file as it will appear in the file table on the dataset page'
        ]);
        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'location',
            'inputOptions' => [
                'required' => true,
                'maxlength' => 200
            ],
            'tooltip' => 'Complete path of the file location including the server'
        ]);
        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'extension',
            'inputOptions' => [
                'required' => true,
                'maxlength' => 30
            ],
            'tooltip' => 'The file extension, usually ignoring any archive related extension, e.g. something.fasta.gz would have extension fasta not gz'
        ]);
        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'size',
            'inputOptions' => [
                'required' => true,
            ],
            'tooltip' => 'The size of file on disk in bytes'
        ]);
        $this->widget('application.components.controls.TextArea', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'description',
            'inputOptions' => [
                'rows' => 6,
                'cols' => 50
            ],
            'tooltip' => 'The description of the files content'
        ]);
        $this->widget('application.components.controls.DateField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'date_stamp',
            'tooltip' => 'The date the file is made publicly available, usually the same as the dataset release date. Format: yyyy-mm-dd'
        ]);
        $this->widget('application.components.controls.DropdownField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'format_id',
            'listDataOptions' => [
                'data' => FileFormat::model()->findAll(),
                'valueField' => 'id',
                'textField' => 'name',
            ],
            'tooltip' => 'The format of the file content, usually a MIME format, select from the dropdown menu'
        ]);
        $this->widget('application.components.controls.DropdownField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'type_id',
            'listDataOptions' => [
                'data' => FileType::model()->findAll(),
                'valueField' => 'id',
                'textField' => 'name',
            ],
            'tooltip' => 'The type of data contained in the file, select from the dropdown menu'
        ]);
        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'sample_name',
            'tooltip' => 'If the file is directly and solely related to a single Sample entity named in GigaDB, a link to that sample can be added here by entering the exact name of the sample'
        ]);
        ?>

        <?php if (!$model->isNewRecord) { ?>
            <div class="control-group">
            <?php if ($model->fileAttributes) { ?>
                    <table class="table table-attr">
                        <caption>Attributes</caption>
                        <thead>
                            <tr>
                                <th class="text-nowrap">Attribute Name</th>
                                <th>Value</th>
                                <th>Unit</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($model->fileAttributes as $fa) { ?>
                                <tr class="row-edit-<?= $fa->id ?>">
                                    <td>
                                        <?= $fa->attribute->attribute_name ?>
                                    </td>
                                    <td>
                                        <?php $this->renderPartial('//shared/_longTextToggler', array(
                                            'id' => 'attr_value_' . $fa->id,
                                            'description' => $fa->value,
                                            'maxLength' => 50
                                        )); ?>
                                    </td>
                                    <td>
                                        <?= $fa->unit ? $fa->unit->name : '' ?>
                                    </td>
                                    <td>
                                        <div class="action-btns">
                                            <button class="btn btn-link btn-edit js-edit" data-test="edit-attr-btn" data="<?= $fa->id ?>">Edit</button>
                                            <button class="btn btn-link js-delete" name="delete_file_attr" data-test="delete-attr-btn" data="<?= $fa->id ?>">Delete</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php } ?>
                        </tbody>
                    </table>
                <?php } ?>
                <br />
                <button type="button" class="btn background-btn-o js-btn-attr" aria-expanded="false" aria-controls="newAttrForm" data-test="new-attr-btn" data-toggle="tooltip" title="Show and/or Add file attributes"><span class="js-btn-attr-label">Show New Attribute Fields</span> <i class="fa fa-caret-down js-caret-type" aria-hidden="true"></i></button>
                <br />
                <fieldset id="newAttrForm" class="js-new-attr mt-10 mb-20" aria-label="New attribute fields" style="display:none;">
                    <div class="row mb-5">
                        <div class="col-xs-12">
                            <?php
                            $this->widget('application.components.controls.DropdownField', [
                                'form' => $form,
                                'model' => $attribute,
                                'attributeName' => '[new]attribute_id',
                                'listDataOptions' => [
                                    'data' => Attributes::model()->findAll(),
                                    'valueField' => 'id',
                                    'textField' => 'attribute_name',
                                ],
                                'inputOptions' => array(
                                    'empty' => 'Select name',
                                    'class' => 'attr-form js-new-attr-name',
                                ),
                                'tooltip' => 'Choose the appropriate attribute name from the dropdown menu'
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-xs-12">
                            <?php
                            $this->widget('application.components.controls.TextArea', [
                                'form' => $form,
                                'model' => $attribute,
                                'attributeName' => '[new]value',
                                'inputOptions' => [
                                    'class' => 'attr-form',
                                    'rows' => 2,
                                    'max' => 1000
                                ],
                                'tooltip' => 'The value of the chosen attribute for this file'
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="row mb-5">
                        <div class="col-xs-12">
                            <?php
                            $this->widget('application.components.controls.DropdownField', [
                                'form' => $form,
                                'model' => $attribute,
                                'attributeName' => '[new]unit_id',
                                'listDataOptions' => [
                                    'data' => Unit::model()->findAll(),
                                    'valueField' => 'id',
                                    'textField' => 'name',
                                ],
                                'inputOptions' => array(
                                    'empty' => 'Select unit',
                                    'class' => 'attr-form'
                                ),
                                'tooltip' => 'If units should be specified, select the appropriate value from the dropdown menu, otherwise leave blank'
                            ]);
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12">
                            <div class="pull-right btns-row">
                                <input type="submit" class="btn background-btn" name="submit_attr" value="Add attribute" />
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
        <?php } ?>
        <div class="pull-right btns-row">
            <a href="/adminFile/admin" class="btn background-btn-o">Cancel</a>
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
        </div>

        <!-- Edit Attribute Modal -->
        <div id="file_attr_modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fileAttrModalTitle">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <div class="modal-header">
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        <h2 class="h4 modal-title" id="fileAttrModalTitle">Edit Attribute</h2>
                    </div>
                    <div class="modal-body">
                    </div>
                    <div class="modal-footer modal-footer-flex">
                        <button type="button" class="btn background-btn-o" data-dismiss="modal">Close</button>
                        <button type="submit" class="btn background-btn js-save js-save-attr-edit-btn" name="edit_attr">Save Attribute</button>
                    </div>
                </div>
            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>


</div>

<script type="text/javascript">
  $(document).ready(function() {
    const toggleBtn = $('.js-btn-attr');
    const caret = $(toggleBtn).children(".js-caret-type");
    const label = $(toggleBtn).children(".js-btn-attr-label");
    const newAttrNameInput = $(".js-new-attr-name");

    function collapseNewAttrForm() {
      $('.js-new-attr').hide();
      $(toggleBtn).attr("aria-expanded", "false");
      caret.removeClass("fa-caret-up").addClass("fa-caret-down");
      label.text("Show New Attribute Fields");
      newAttrNameInput.attr({
          "required": false,
          "aria-required": "false"
      });
    }

    function expandNewAttrForm() {
      $('.js-new-attr').show();
      $(toggleBtn).attr("aria-expanded", "true");
      caret.removeClass("fa-caret-down").addClass("fa-caret-up");
      label.text("Hide New Attribute Fields");
      newAttrNameInput.attr({
          "required": true,
          "aria-required": "true"
      });
    }

    function toggleNewAttrForm() {
      const isExpanded = $('.js-btn-attr').attr("aria-expanded") === "true";
      if (isExpanded) {
          collapseNewAttrForm();
      } else {
          expandNewAttrForm();
      }
    }


    // NOTE click listener on the document because the button is in a partial view
    $(document).on('click', '.js-save-attr-edit-btn', function(e) {
      collapseNewAttrForm();
    });
    $('.js-btn-attr').click(function(e) {
        e.preventDefault();
        toggleNewAttrForm()
    })

    $('.js-edit').click(function(e) {
        e.preventDefault();
        const id = $(this).attr('data');

        const row = $('.row-edit-' + id);
        if (id) {
            $.post('/adminFile/editAttr', {
                'id': id
            }, function(result) {
                if (result.success) {
                  $('#file_attr_modal').modal('show');
                  $('#file_attr_modal .modal-body').html(result.data);
                }
            }, 'json');
        }
    })

    // Handle attribute delete
    $('.js-delete').click(function(e) {
        e.preventDefault();
        const id = $(this).attr('data');
        const row = $('.row-edit-' + id);
        if (id) {
            $.post('/adminFile/deleteFileAttribute', {
                'id': id
            }, function(result) {
                if (result) {
                    // console.log(result);
                }
            }, 'json');
        }
        // Give enough time to the database to update before reloading the page
        setTimeout(() => {
            window.location.reload();
        }, 200);
    })

    $('.js-cancel-attr-edit-btn').click(function(e) {
        e.preventDefault();
        collapseNewAttrForm();
    })
  })
</script>


<?php
$jsFile = Yii::getPathOfAlias('application.js.trap-focus') . '.js';
$jsUrl = Yii::app()->assetManager->publish($jsFile);
Yii::app()->clientScript->registerScriptFile($jsUrl, CClientScript::POS_END);
?>

<script>
	$('#file_attr_modal').on('shown.bs.modal', function() {
    lastFocusedElement = document.activeElement;

    $('#FileAttributes_edit_attribute_id').focus();
    trapFocus($(this));
	});

	$('#file_attr_modal').on('hidden.bs.modal', function() {
    $(this).off('keydown');
    if (lastFocusedElement) {
        lastFocusedElement.focus();
    }
	});
</script>