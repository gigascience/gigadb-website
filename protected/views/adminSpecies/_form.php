<div class="section form row">

	<div class="col-md-offset-3 col-md-6">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'species-form',
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
			'attributeName' => 'tax_id',
			'inputOptions' => [
				'required' => true,
			],
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'common_name',
			'inputOptions' => [
				'required' => true,
				'maxlength' => 64
			],
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'genbank_name',
			'inputOptions' => [
				'required' => true,
				'maxlength' => 128
			],
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'scientific_name',
			'inputOptions' => [
				'required' => true,
				'maxlength' => 128
			],
		]);
		?>

		<div class="pull-right">
			<a href="/adminSpecies/admin" class="btn background-btn-o">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>

</div>