<div class="section form row">

	<div class="col-md-offset-3 col-md-6">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'dataset-project-form',
			'enableAjaxValidation' => false,
		)); ?>

		<?php if ($model->hasErrors()) : ?>
			<div class="alert alert-danger">
				<?php echo $form->errorSummary($model); ?>
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
		]);
		?>

		<div class="pull-right">
			<a href="/adminDatasetProject/admin" class="btn background-btn-o">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>

</div>