<div class="section form">
  <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'dataset-author-form',
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
  $this->widget('application.components.controls.DropdownField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'dataset_id',
    'listDataOptions' => [
      'data' => Util::getDois(),
      'valueField' => 'id',
      'textField' => 'identifier',
    ],
    'groupOptions' => [
      'class' => 'col-md-12'
    ],
  ]);
  ?>

  <?php
  $this->widget('application.components.controls.DropdownField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'author_id',
    'listDataOptions' => [
      'data' => Author::model()->findAll(array('order' => 'surname')),
      'valueField' => 'id',
      'textField' => 'fullAuthor',
    ],
    'enableSorting' => true,
    'groupOptions' => [
      'class' => 'col-md-12'
    ],
  ]);
  ?>

  <?php
  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'rank',
    'inputOptions' => [
      'required' => 'required',
      'aria-required' => 'true',
    ],
    'groupOptions' => [
      'class' => 'col-md-12'
    ],
  ]);
  ?>

  <div class="col-md-12">
    <div class="pull-right btns-row">
      <a href="/adminDatasetAuthor/admin" class="btn background-btn-o btn-min-width">Cancel</a>
      <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width')); ?>
    </div>
  </div>

  <?php $this->endWidget(); ?>
</div>