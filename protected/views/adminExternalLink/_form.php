<div class="well">
  <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'external-link-form',
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
      'data' => Dataset::model()->findAll(),
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

  $this->widget('application.components.controls.TextField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'url',
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
    'attributeName' => 'external_link_type_id',
    'listDataOptions' => [
      'data' => ExternalLinkType::model()->findAll(),
      'valueField' => 'id',
      'textField' => 'name',
    ],
    'inputOptions' => [
      'required' => true,
    ],
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9'
  ]);
  ?>

  <hr />
  <div class="pull-right btns-row">
    <a href="/adminExternalLink/admin" class="btn background-btn-o btn-min-width">Cancel</a>
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width m-0')); ?>
  </div>
  <div class="clearfix"></div>

  <?php $this->endWidget(); ?>
</div>