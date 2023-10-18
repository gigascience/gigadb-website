<div class="section form row">

    <div class="col-md-offset-3 col-md-6">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'prefix-form',
            'enableAjaxValidation' => false,
        )); ?>

        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php if ($model->hasErrors()) : ?>
            <div class="alert alert-danger">
                <?php echo $form->errorSummary($model); ?>
            </div>
        <?php endif; ?>


        <?php
        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'prefix',
            'inputOptions' => [
                'required' => true,
                'maxlength' => 20
            ],
        ]);
        $this->widget('application.components.controls.DropdownField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'source',
            'dataset' => array('EBI' => 'EBI', 'NCBI' => 'NCBI', 'DDBJ' => 'DDBJ'),
            'inputOptions' => [
                'required' => true,
            ],
        ]);
        $this->widget('application.components.controls.TextArea', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'url',
            'inputOptions' => [
                'rows' => 3,
                'cols' => 50
            ],
        ]);
        ?>

        <div class="pull-right">
            <a href="/adminLinkPrefix/admin" class="btn background-btn-o">Cancel</a>
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>

</div>