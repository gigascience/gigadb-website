<div class="section form">
	<?php $form = $this->beginWidget('CActiveForm', array(
		'id' => 'dataset-sample-form',
		'enableAjaxValidation' => false,
		'htmlOptions' => [
			'class' => 'row'
		]
	)); ?>

	<div class="col-md-12">
		<p class="note">Fields with <span class="required">*</span> are required.</p>

		<?php if ($model->hasErrors()) : ?>
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
			'class' => 'col-md-6'
		],
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
		'groupOptions' => [
			'class' => 'col-md-6'
		],
	]);
	?>

	<div class="col-md-12">
		<div class="pull-right btns-row">
			<a href="/adminDatasetSample/admin" class="btn background-btn-o btn-min-width">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn btn-min-width')); ?>
		</div>
	</div>

	<?php $this->endWidget(); ?>
</div>