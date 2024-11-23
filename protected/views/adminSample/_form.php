<div class="section form">
    <?php $form = $this->beginWidget('CActiveForm', array(
        'id' => 'sample-form',
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

    <div class="col-md-6">
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
    </div>

    <div class="col-md-6">
        <?php
        $this->widget('application.components.controls.TextField', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'name',
            'inputOptions' => [
                'maxlength' => 50
            ],
        ]);
        ?>
    </div>

    <div class="col-md-12">
        <?php
        $model->attributesList = $model->attributesList ? $model->attributesList :  $model->getAttributesList(true);
        $this->widget('application.components.controls.TextArea', [
            'form' => $form,
            'model' => $model,
            'attributeName' => 'attributesList',
            'inputOptions' => [
                'rows' => 6,
                'cols' => 50
            ],
        ]);
        ?>
    </div>

    <div class="col-md-12">
        <div class="pull-right btns-row">
            <a href="/adminSample/admin" class="btn background-btn-o">Cancel</a>
            <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
        </div>
    </div>

    <?php $this->endWidget(); ?>
</div>