<div class="section form row">

    <div class="col-md-offset-3 col-md-6">
        <?php $form = $this->beginWidget('CActiveForm', array(
            'id' => 'news-form',
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
            'attributeName' => 'title',
            'inputOptions' => [
                'required' => true,
                'maxlength' => 200
            ],
        ]);
        $this->widget('application.components.controls.TextArea', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'body',
            'inputOptions' => [
                'rows' => 6,
                'cols' => 50
            ],
        ]);
        $this->widget('application.components.controls.DateField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'start_date',
            'inputOptions' => [
                'required' => true,
            ],
        ]);
        $this->widget('application.components.controls.DateField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'end_date',
            'inputOptions' => [
                'required' => true,
            ],
        ]);
        ?>

        <div class="pull-right btns-row">
            <a href="/news/admin" class="btn background-btn-o">Cancel</a>
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
        </div>

        <?php $this->endWidget(); ?>
    </div>
</div>