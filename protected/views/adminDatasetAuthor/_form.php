<div class="well">
  <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'dataset-author-form',
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
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9',
    'attributeName' => 'dataset_id',
    'listDataOptions' => [
      'data' => Util::getDois(),
      'valueField' => 'id',
      'textField' => 'identifier',
    ],
  ]);
  ?>

  <?php
  $this->widget('application.components.controls.DropdownField', [
    'form' => $form,
    'model' => $model,
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9',
    'attributeName' => 'author_id',
    'listDataOptions' => [
      'data' => Author::model()->findAll(array('order' => 'surname')),
      'valueField' => 'id',
      'textField' => 'fullAuthor',
    ],
    'enableSorting' => true,
  ]);
  ?>

  <?php
  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9',
    'attributeName' => 'rank',
    'inputOptions' => [
      'required' => 'required',
      'aria-required' => 'true',
    ],
  ]);
  ?>

  <hr />

  <div class="pull-right btns-row">
    <a href="/adminDatasetAuthor/admin" class="btn background-btn-o btn-min-width">Cancel</a>
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width m-0')); ?>
  </div>

  <div class="clearfix"></div>

  <?php $this->endWidget(); ?>
</div>