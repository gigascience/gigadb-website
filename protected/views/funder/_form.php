<div class="well">
  <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'funder-form',
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
    'attributeName' => 'uri',
    'inputOptions' => [
      'required' => true,
    ],
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9',
  ]);

  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'primary_name_display',
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9',
  ]);

  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'country',
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9',
  ]);
  ?>

  <hr />

  <div class="pull-right btns-row">
    <a href="/funder/admin" class="btn background-btn-o btn-min-width">Cancel</a>
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width m-0')); ?>
  </div>

  <div class="clearfix"></div>

  <?php $this->endWidget(); ?>
</div>