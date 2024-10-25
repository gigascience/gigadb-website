<div class="section form row">

	<div class="col-md-offset-3 col-md-6">
		<?php $form = $this->beginWidget('CActiveForm', array(
			'id' => 'project-form',
			'enableAjaxValidation' => false,
      'htmlOptions' => array('enctype' => 'multipart/form-data'),
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
    // field replaced by vue widget
		// $this->widget('application.components.controls.TextField', [
		// 	'form' => $form,
		// 	'model' => $model,
		// 	'attributeName' => 'image_location',
		// 	'inputOptions' => [
		// 		'maxlength' => 100
		// 	]
		// ]);
		?>

<!-- mock input, vue app should render input with same id
    <div class="form-group">
        <?php echo $form->labelEx($model, 'image'); ?>
        <?php echo $form->fileField($model, 'image'); ?>
        <?php echo $form->error($model, 'image'); ?>
    </div>
-->
    <div id="vue-client_project-image-location">
      <div class="spinner"></div>
    </div>
    <div>
      <script type="module" src="http://localhost:5173/@vite/client"></script>
      <script type="module" src="http://localhost:5173/src/main.ts"></script>
    </div>

		<div class="pull-right btns-row">
			<a href="/adminProject/admin" class="btn background-btn-o">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>

</div>