<div class="section form row">

	<div class="col-md-offset-3 col-md-6">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'dataset-funder-form',
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
			'dataset' => $datasets,
			'inputOptions' => [
				'required' => true,
			],
      'tooltip' => 'Select or type the relevant Dataset DOI ID'
		]);
		$this->widget('application.components.controls.DropdownField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'funder_id',
			'dataset' => $funders,
			'inputOptions' => [
				'required' => true,
			],
      'tooltip' => 'Select the Funder name from the drop-down list. If the name is not present, it will need to be added via the Funder Admin page'
		]);
		$this->widget('application.components.controls.TextArea', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'grant_award',
			'inputOptions' => [
				'rows' => 6,
				'cols' => 50
			],
      'tooltip' => 'Type the Grant/Award ID provided by the submitter'
		]);
		$this->widget('application.components.controls.TextArea', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'awardee',
			'inputOptions' => [
				'rows' => 6,
				'cols' => 50
			],
      'tooltip' => 'Insert the Principle Investigators name who was awarded the grant, use format Initials Surname e.g. CI Hunter'
		]);
		$this->widget('application.components.controls.TextArea', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'comments',
			'inputOptions' => [
				'rows' => 6,
				'cols' => 50
			],
      'tooltip' => 'Use this field to include a program name if the award was part of a specific program, or other short details as required'
		]);
		?>

		<div class="pull-right btns-row">
			<a href="/datasetFunder/admin" class="btn background-btn-o">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>

</div>