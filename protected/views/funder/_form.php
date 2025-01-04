<div class="section form">
  <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'funder-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => [
      'class' => 'row'
    ]
  )); ?>

  <div class="col-md-12">
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
    'groupOptions' => [
      'class' => 'col-md-6'
    ],
  ]);

  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'primary_name_display',
    'groupOptions' => [
      'class' => 'col-md-6'
    ],
  ]);

  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'country',
    'groupOptions' => [
      'class' => 'col-md-12'
    ],
  ]);
  ?>

  <div class="col-md-12">
    <div class="pull-right btns-row">
      <a href="/funder/admin" class="btn background-btn-o btn-min-width">Cancel</a>
      <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width')); ?>
    </div>
  </div>

  <?php $this->endWidget(); ?>
</div>