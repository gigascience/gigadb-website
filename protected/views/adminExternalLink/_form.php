<div class="section form row">

	<div class="col-md-offset-3 col-md-6">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'external-link-form',
			'enableAjaxValidation' => false,
		)); ?>

		<p class="note">Fields with <span class="required">*</span> are required.</p>

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
				'data' => Dataset::model()->findAll(),
				'valueField' => 'id',
				'textField' => 'identifier',
			],
			'inputOptions' => [
				'required' => true,
			],
		]);
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'url',
			'inputOptions' => [
				'required' => true,
			],
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
		]);
		?>

		<div class="pull-right">
			<a href="/adminExternalLink/admin" class="btn background-btn-o">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>

</div>