<div class="section form row">

	<div class="col-xs-offset-3 col-xs-6">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'dataset-author-form',
			'enableAjaxValidation' => false,
		)); ?>

		<p class="note">Fields with <span class="required">*</span> are required.</p>

		<div class="alert alert-danger">
			<?php echo $form->errorSummary($model); ?>
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
		]);
		?>

		<?php
		$this->widget('application.components.controls.DropdownField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'author_id',
			'listDataOptions' => [
				'data' => Author::model()->findAll(array('order' => 'surname')),
				'valueField' => 'id',
				'textField' => 'fullAuthor',
			],
		]);
		?>

		<?php
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'rank',
			'inputOptions' => [
				'required' => 'required',
				'aria-required' => 'true',
			],
		]);
		?>

		<div class="pull-right">
			<a href="/adminDatasetAuthor/admin" class="btn background-btn-o">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>

</div><!-- form -->