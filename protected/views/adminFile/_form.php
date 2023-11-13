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
            ]
        ]);
        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'name',
            'inputOptions' => [
                'required' => true,
                'maxlength' => 100
            ],
        ]);
        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'location',
            'inputOptions' => [
                'required' => true,
                'maxlength' => 200
            ],
        ]);
        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'extension',
            'inputOptions' => [
                'required' => true,
                'maxlength' => 30
            ],
        ]);
        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'size',
            'inputOptions' => [
                'required' => true,
            ],
        ]);
        $this->widget('application.components.controls.TextArea', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'description',
            'inputOptions' => [
                'rows' => 6,
                'cols' => 50
            ],
        ]);
        $this->widget('application.components.controls.DateField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'date_stamp',
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
        ]);
        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'sample_name',
        ]);
        ?>

        <?php if (!$model->isNewRecord) { ?>
            <div class="control-group">
                <button type="button" class="btn background-btn-o js-btn-attr" aria-expanded="false" aria-controls="newAttrForm" data-test="new-attr-btn"><span class="js-btn-attr-label">Show New Attribute Fields</span> <i class="fa fa-caret-down js-caret-type" aria-hidden="true"></i></button>
                <br />
                <fieldset id="newAttrForm" class="js-new-attr mt-10 row" aria-label="New attribute fields" style="display:none;">
                    <div class="col-xs-5">
                        <?php
                        $this->widget('application.components.controls.DropdownField', [
                            'form' => $form,
                            'model' => $attribute,
                            'attributeName' => 'attribute_id',
                            'listDataOptions' => [
                                'data' => Attribute::model()->findAll(),
                                'valueField' => 'id',
                                'textField' => 'attribute_name',
                            ],
                            'inputOptions' => array(
                                'empty' => 'Select name',
                                'class' => 'attr-form js-new-attr-name',
                            )
                        ]);
                        ?>
                    </div>
                    <div class="col-xs-3">
                        <?php
                        $this->widget('application.components.controls.TextField', [
                            'form' => $form,
                            'model' => $attribute,
                            'attributeName' => 'value',
                            'inputOptions' => [
                                'class' => 'attr-form'
                            ],
                        ]);
                        ?>
                    </div>
                    <div class="col-xs-4">
                        <?php
                        $this->widget('application.components.controls.DropdownField', [
                            'form' => $form,
                            'model' => $attribute,
                            'attributeName' => 'unit_id',
                            'listDataOptions' => [
                                'data' => Unit::model()->findAll(),
                                'valueField' => 'id',
                                'textField' => 'name',
                            ],
                            'inputOptions' => array(
                                'empty' => 'Select unit',
                                'class' => 'attr-form'
                            )
                        ]);
                        ?>
                    </div>
                    <div class="col-xs-12">
                        <input type="submit" class="btn background-btn" name="submit_attr" value="Add attribute" />
                    </div>
                </fieldset>
                <br />
                <?php if ($model->fileAttributes) { ?>
                    <table class="table table-attr">
                        <caption>Attributes</caption>
                        <thead>
                            <tr>
                                <th>Attribute Name</th>
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
                                        <?= $fa->value ?>
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
            </div>
        <?php } ?>
        <div class="pull-right btns-row">
            <a href="/adminFile/admin" class="btn background-btn-o">Cancel</a>
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>

</div>

<script type="text/javascript">
    $(document).ready(function() {
        // Handle new attribute dropdown button
        $('.js-btn-attr').click(function(e) {
            e.preventDefault();

            const caret = $(this).children(".js-caret-type");
            const label = $(this).children(".js-btn-attr-label");
            const newAttrNameInput = $(".js-new-attr-name");
            const isExpanded = $(this).attr("aria-expanded") == "true";

            $('.js-new-attr').toggle();
            $(this).attr("aria-expanded", !isExpanded);
            caret.toggleClass("fa-caret-up fa-caret-down");
            label.text(isExpanded ? "Show New Attribute Fields" : "Hide New Attribute Fields");
            // NOTE: toggling the required attributes from the input on and off so that client-side validation is not triggered when the fields are hidden
            newAttrNameInput.attr({
                "required": !isExpanded,
                "aria-required": (!isExpanded).toString()
            });
        })

        // Handle attribute edit
        $('.js-edit').click(function(e) {
            e.preventDefault();
            const id = $(this).attr('data');

            const row = $('.row-edit-' + id);
            if (id) {
                $.post('/adminFile/editAttr', {
                    'id': id
                }, function(result) {
                    if (result.success) {
                        row.html(result.data);
                        //$('.js-new-attr').remove();
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
    })
</script>