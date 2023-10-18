<div class="section form row">

	<div class="col-md-offset-3 col-md-6">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'attribute-form',
			'enableAjaxValidation' => false,
		)); ?>

		<?php if ($model->hasErrors()) : ?>
			<div class="alert alert-danger">
				<?php echo $form->errorSummary($model); ?>
			</div>
		<?php endif; ?>

		<?php
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'attribute_name',
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'definition',
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'model',
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'structured_comment_name',
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'value_syntax',
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'allowed_units',
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'occurance',
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'ontology_link',
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'note',
		]);
		?>

		<div class="pull-right">
			<a href="/attribute/admin" class="btn background-btn-o">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>

</div>