<div class="section form row">

	<div class="col-md-offset-3 col-md-6">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'project-form',
			'enableAjaxValidation' => false,
		)); ?>

		<p class="note">Fields with <span class="required">*</span> are required.</p>

		<?php if ($model->hasErrors()) : ?>
			<div class="alert alert-danger">
				<?php echo $form->errorSummary($model); ?>
			</div>
		<?php endif; ?>

		<?php
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'url',
			'inputOptions' => [
				'required' => true,
				'maxlength' => 128
			],
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'name',
			'inputOptions' => [
				'maxlength' => 255
			]
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'image_location',
			'inputOptions' => [
				'maxlength' => 100
			]
		]);
		?>

		<div class="pull-right">
			<a href="/adminProject/admin" class="btn background-btn-o">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>

</div>