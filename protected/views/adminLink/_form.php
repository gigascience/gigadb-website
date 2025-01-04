<div class="well">
  <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'link-form',
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
  $this->widget('application.components.controls.DropdownField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'dataset_id',
    'listDataOptions' => [
      'data' => Util::getDois(),
      'valueField' => 'id',
      'textField' => 'identifier',
    ],
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9',
    'inputOptions' => [
      'required' => true,
    ]
  ]);

  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'link',
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9',
    'inputOptions' => [
      'required' => true,
      'maxlength' => 100
    ]
  ]);

  $this->widget('application.components.controls.CheckBoxField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'is_primary',
    'isHorizontal' => true,
  ]);
  ?>

  <hr />

  <div class="pull-right btns-row">
    <a href="/adminLink/admin" class="btn background-btn-o btn-min-width">Cancel</a>
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width m-0')); ?>
  </div>

  <div class="clearfix"></div>

  <?php $this->endWidget(); ?>
</div>