<div class="section form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'news-form',
        'enableAjaxValidation' => false,
        'htmlOptions' => [
            'class' => 'row'
        ]
    )); ?>

    <div class="col-md-12">
        <p class="note">Fields with <span class="required">*</span> are required.</p>

        <?php if ($model->hasErrors()) : ?>
            <div class="alert alert-danger">
                <?php echo $form->errorSummary($model); ?>
            </div>
        <?php endif; ?>
    </div>

    <?php
    $this->widget('application.components.controls.TextField', [
        'form' => $form,
        'model' => $model,
        'attributeName' => 'title',
        'inputOptions' => [
            'required' => true,
            'maxlength' => 200
        ],
        'groupOptions' => [
            'class' => 'col-md-12'
        ],
    ]);

    $this->widget('application.components.controls.DateField', [
        'form' => $form,
        'model' => $model,
        'attributeName' => 'start_date',
        'inputOptions' => [
            'required' => true,
        ],
        'groupOptions' => [
            'class' => 'col-md-6'
        ],
    ]);

    $this->widget('application.components.controls.DateField', [
        'form' => $form,
        'model' => $model,
        'attributeName' => 'end_date',
        'inputOptions' => [
            'required' => true,
        ],
        'groupOptions' => [
            'class' => 'col-md-6'
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
        'groupOptions' => [
            'class' => 'col-md-12'
        ],
    ]);
    ?>

    <div class="col-md-12">
        <div class="pull-right btns-row">
            <a href="/news/admin" class="btn background-btn-o btn-min-width">Cancel</a>
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>
</div>