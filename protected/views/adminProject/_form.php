<div class="section form">
  <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'project-form',
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
    'attributeName' => 'url',
    'inputOptions' => [
      'required' => true,
      'maxlength' => 128
    ],
    'groupOptions' => [
      'class' => 'col-md-6'
    ],
  ]);

  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'name',
    'inputOptions' => [
      'maxlength' => 255
    ],
    'groupOptions' => [
      'class' => 'col-md-6'
    ],
  ]);

  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'image_location',
    'inputOptions' => [
      'maxlength' => 100
    ],
    'groupOptions' => [
      'class' => 'col-md-12'
    ],
  ]);
  ?>

  <div class="col-md-12">
    <div class="pull-right btns-row">
      <a href="/adminProject/admin" class="btn background-btn-o btn-min-width">Cancel</a>
      <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width')); ?>
    </div>
  </div>

  <?php $this->endWidget(); ?>
</div>