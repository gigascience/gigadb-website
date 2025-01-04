<div class="well">
  <?php $form = $this->beginWidget('CActiveForm', array(
    'id' => 'dataset-funder-form',
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
    'dataset' => $datasets,
    'inputOptions' => [
      'required' => true,
    ],
    'tooltip' => 'Select or type the relevant Dataset DOI ID',
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9'
  ]);

  $this->widget('application.components.controls.DropdownField', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'funder_id',
    'dataset' => $funders,
    'inputOptions' => [
      'required' => true,
    ],
    'tooltip' => 'Select the Funder name from the drop-down list. If the name is not present, it will need to be added via the Funder Admin page',
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9'
  ]);

  $this->widget('application.components.controls.TextArea', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'grant_award',
    'inputOptions' => [
      'rows' => 4,
      'cols' => 50
    ],
    'tooltip' => 'Type the Grant/Award ID provided by the submitter',
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9'
  ]);

  $this->widget('application.components.controls.TextArea', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'awardee',
    'inputOptions' => [
      'rows' => 4,
      'cols' => 50
    ],
    'tooltip' => 'Insert the Principle Investigators name who was awarded the grant, use format Initials Surname e.g. CI Hunter',
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9'
  ]);

  $this->widget('application.components.controls.TextArea', [
    'form' => $form,
    'model' => $model,
    'attributeName' => 'comments',
    'inputOptions' => [
      'rows' => 4,
      'cols' => 50
    ],
    'tooltip' => 'Use this field to include a program name if the award was part of a specific program, or other short details as required',
    'labelOptions' => [
      'class' => 'col-xs-3',
    ],
    'inputWrapperOptions' => 'col-xs-9'
  ]);
  ?>

  <hr />

  <div class="pull-right btns-row">
    <a href="/datasetFunder/admin" class="btn background-btn-o btn-min-width">Cancel</a>
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width m-0')); ?>
  </div>

  <div class="clearfix"></div>

  <?php $this->endWidget(); ?>
</div>