<div class="section form row">

    <div class="col-md-offset-3 col-md-6">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'sample-form',
            'enableAjaxValidation' => false,
        )); ?>

        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php if ($model->hasErrors()) : ?>
            <div id="sample_error" class="alert alert-danger">
                <?php echo $form->errorSummary($model); ?>
            </div>
        <?php endif; ?>

        <div id="ajax-error" class='alert alert-danger' style="display: none;">An error occurred</div>

        <div class="form-group">
            <?php echo $form->labelEx($model, 'species_id', array('class' => 'control-label')); ?>
            <?php
            $criteria = new CDbCriteria;
            $criteria->select = 't.id, t.common_name';
            $criteria->limit = 100;

            $this->widget('zii.widgets.jui.CJuiAutoComplete', array(
                'name' => 'name',
                'model' => $model,
                'attribute' => 'species_id',
                'source' => $this->createUrl('/adminDatasetSample/autocomplete'),
                'options' => array(
                    'minLength' => '2',
                ),
                'htmlOptions' => array(
                    'aria-describedby' => $form->error($model, 'species_id') ? 'species_id-desc' : '',
                    'required' => true,
                    'aria-required' => 'true',
                    'class' => 'form-control',
                    'placeholder' => 'name',
                    'size' => 'auto',
                ),
            ));
            ?>

            <div role="alert" id="species_id-error">
                <?php echo $form->error($model, 'species_id'); ?>
            </div>
        </div>

        <?php
        $model->attributesList = $model->attributesList ? $model->attributesList :  $model->getAttributesList(true); // this sets the default text content of the textarea
        $this->widget('application.components.controls.TextArea', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'attributesList',
        ]);

        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'name',
            'inputOptions' => [
                'maxlength' => 50
            ],
        ]);
        ?>

        <div class="pull-right btns-row">
            <a href="/adminSample/admin" class="btn background-btn-o">Cancel</a>
            <button id='checkAttribute' type='button' class="btn btn-primary"><?php echo $model->isNewRecord ? 'Create' : 'Save'?></button>

        </div>

        <div class='modal fade' id='confirmation_sample_modal' role='dialog'>
            <div class='modal-dialog modal-lg'>
                <div class='modal-content'>
                    <div class='modal-header'>
                        <button type='button' class='close' data-dismiss='modal'>&times;</button>
                        <h4 class='modal-title'>Important</h4>
                    </div>
                    <div class='modal-body'>
                        <div id='check-attribute-warning' class='alert alert-warning' style='display: none;'>
                        </div>
                        <div id="check-attribute-confirmation" class="mt-4">
                        </div>
                    </div>
                    <div class='modal-footer'>
                        <a id="hideModal" class='btn background-btn-o'>Cancel</a>
                        <?php echo CHtml::submitButton('Confirm', array('class' => 'btn background-btn')); ?>
                    </div>
                </div>

            </div>
        </div>
        <?php $this->endWidget(); ?>
    </div>

</div>
<script>
    $(document).ready(function() {
        $('#hideModal').click(function(e) {
            $('#confirmation_sample_modal').modal('hide');
        })

        $('#checkAttribute').click(function (e) {
            e.preventDefault()

            let myWarning = $('#check-attribute-warning')[0];
            let myConfirmation = $('#check-attribute-confirmation')[0];
            myWarning.innerHTML = '';
            myWarning.style.display = 'none'
            myConfirmation.innerHTML = '';
            $('#ajax-error')[0].style.display = 'none';

            $.ajax({
                url: "<?php echo  Yii::app()->createUrl('adminSample/checkAttribute') ?> ",
                type: 'POST',
                data:  {
                    attr: $('.form').find("textarea[name='Sample[attributesList]']").val()
                },
                dataType: 'json',
                success: function(response) {
                    $('#confirmation_sample_modal').modal('show');

                    if (0 < response.messages.length) {
                        myWarning.style.display = 'block'
                    }

                    response.messages.forEach((message) => {
                        let el = document.createElement('li');
                        el.textContent = message;

                        myWarning.appendChild(el);
                    })

                    let el = document.createElement('div');
                    el.textContent= 'Are you sure you want to continue?'
                    el.className = 'mt-4'
                    myConfirmation.appendChild(el);

                },
                error: function(xhr, status, error) {
                    $('#ajax-error')[0].style.display = 'block';
                }
            });
        });
    });
</script>
