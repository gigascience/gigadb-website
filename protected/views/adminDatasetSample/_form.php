<div class="well">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'dataset-sample-form',
		'enableAjaxValidation' => false,
		'htmlOptions' => [
			'class' => 'form-horizontal'
		]
	)); ?>

  <div class="col-md-12 mb-10">
    <p class="note">Fields with <span class="required">*</span> are required.</p>
  </div>

	<?php if ($model->hasErrors()) : ?>
		<div class="form-group">
			<div class="col-xs-12">
				<div class="alert alert-danger">
					<?php echo $form->errorSummary($model); ?>
				</div>
			</div>
		</div>
	<?php endif; ?>

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
		'inputWrapperOptions' => 'col-xs-9'
	]);
	?>

	<?php
	$this->widget('application.components.controls.DropdownField', [
		'form' => $form,
		'model' => $model,
		'attributeName' => 'sample_id',
		'listDataOptions' => [
			'data' => Sample::model()->findAll(array('limit' => 10000, 'order' => 'id DESC')),
			'valueField' => 'id',
			'textField' => 'id',
		],
		'labelOptions' => [
			'class' => 'col-xs-3',
		],
		'inputWrapperOptions' => 'col-xs-9'
	]);
	?>

  <hr />

  <div class="pull-right btns-row">
    <a href="/adminDatasetSample/admin" class="btn background-btn-o btn-min-width">Cancel</a>
    <?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width m-0')); ?>
  </div>

  <div class="clearfix"></div>

	<?php $this->endWidget(); ?>
</div>