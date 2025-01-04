<div class="well">
  <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'species-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => [
      'class' => 'form-horizontal'
    ]
  )); ?>

  <div class="col-md-12 mb-10">
    <p class="note">Fields with <span class="required">*</span> are required.</p>

    <?php if ($model->hasErrors()): ?>
      <div class="alert alert-danger">
        <?php echo $form->errorSummary($model); ?>
      </div>
    <?php endif; ?>
  </div>

  <?php
  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'tax_id',
    'inputOptions' => [
      'required' => true,
    ],
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9'
  ]);

  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'common_name',
    'inputOptions' => [
      'required' => true,
      'maxlength' => 64
    ],
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9'
  ]);

  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'scientific_name',
    'inputOptions' => [
      'required' => true,
      'maxlength' => 128
    ],
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9'
  ]);

  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'genbank_name',
    'inputOptions' => [
      'maxlength' => 128
    ],
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9'
  ]);
  ?>

  <hr />

  <div class="pull-right btns-row">
    <a href="/adminSpecies/admin" class="btn background-btn-o btn-min-width">Cancel</a>
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width m-0')); ?>
  </div>

  <div class="clearfix"></div>

  <?php $this->endWidget(); ?>
</div>