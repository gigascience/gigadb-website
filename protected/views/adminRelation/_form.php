<div class="well">
<?php $form = $this->beginWidget('CActiveForm', array(
  'id' => 'relation-form',
  'enableAjaxValidation' => false,
  'htmlOptions' => [
    'class' => 'form-horizontal'
  ]
)); ?>

<?php if (Yii::app()->user->hasFlash('error') || $model->hasErrors()): ?>
  <div class="col-md-12">
    <?php if (Yii::app()->user->hasFlash('error')): ?>
      <div class="alert alert-danger">
        <?php echo Yii::app()->user->getFlash('error'); ?>
      </div>
    <?php endif; ?>

    <?php if ($model->hasErrors()): ?>
      <div class="alert alert-danger">
        <?php echo $form->errorSummary($model); ?>
      </div>
    <?php endif; ?>
  </div>
<?php endif; ?>

<div class="col-md-12 mb-10">
  <p class="note">Fields with <span class="required">*</span> are required.</p>
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
  'inputOptions' => [
    'required' => true,
  ],
  'labelOptions' => [
    'class' => 'col-xs-3',
  ],
  'inputWrapperOptions' => 'col-xs-9'
]);

$this->widget('application.components.controls.DropdownField', [
  'form' => $form,
  'model' => $model,
  'attributeName' => 'related_doi',
  'listDataOptions' => [
    'data' => Util::getDois(),
    'valueField' => 'identifier',
    'textField' => 'identifier',
  ],
  'inputOptions' => [
    'required' => true,
  ],
  'labelOptions' => [
    'class' => 'col-xs-3',
  ],
  'inputWrapperOptions' => 'col-xs-9'
]);

$this->widget('application.components.controls.DropdownField', [
  'form' => $form,
  'model' => $model,
  'attributeName' => 'relationship_id',
  'listDataOptions' => [
    'data' => Relationship::model()->findAll(),
    'valueField' => 'id',
    'textField' => 'name',
  ],
  'labelOptions' => [
    'class' => 'col-xs-3',
  ],
  'inputWrapperOptions' => 'col-xs-9'
]);

if ('insert' === $model->getScenario()) {
  $this->widget('application.components.controls.CheckBoxField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'add_reciprocal',
    'label' => 'Do you want to add a reciprocal relation model',
    'groupOptions' => [
      'class' => 'col-md-12'
    ],
  ]);
}
?>

<hr />

<div class="pull-right btns-row">
  <a href="/adminRelation/admin" class="btn background-btn-o btn-min-width">Cancel</a>
  <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width m-0')); ?>
</div>

<div class="clearfix"></div>

<?php $this->endWidget(); ?>
</div>