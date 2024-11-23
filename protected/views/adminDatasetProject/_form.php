<div class="section form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'dataset-project-form',
		'enableAjaxValidation' => false,
		'htmlOptions' => [
			'class' => 'row'
		]
	)); ?>

	<?php if ($model->hasErrors()) : ?>
		<div class="col-md-12">
			<div class="alert alert-danger">
				<?php echo $form->errorSummary($model); ?>
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
		'inputOptions' => [
			'required' => true,
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
		'attributeName' => 'project_id',
		'listDataOptions' => [
			'data' => Project::model()->findAll(),
			'valueField' => 'id',
			'textField' => 'name',
		],
		'inputOptions' => [
			'required' => true,
		],
		'groupOptions' => [
			'class' => 'col-md-12'
		],
	]);
	?>

	<div class="col-md-12">
		<div class="pull-right btns-row">
			<a href="/adminDatasetProject/admin" class="btn background-btn-o btn-min-width">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width')); ?>
		</div>
	</div>

	<?php $this->endWidget(); ?>
</div>