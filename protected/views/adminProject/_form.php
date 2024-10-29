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
    // TODO field replaced by vue widget, should remove / replace by thumbnail
		$this->widget('application.components.controls.TextField', [
			'form' => $form,
			'model' => $model,
			'attributeName' => 'image_location',
			'inputOptions' => [
				'maxlength' => 100,
        'readonly' => true
			]
		]);
		?>

    <div id="vue-client_project-image-logo">
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

<script>
document.addEventListener('logo-uploaded', function(e) {
    // handle successfully uploaded logo image from uppy
    const imageLocation = e.detail.imageLocation;

    const imageLocationInput = document.querySelector('input[name="Project[image_location]"]');
    if (imageLocationInput instanceof HTMLInputElement) {
      imageLocationInput.value = imageLocation;
    }
});
</script>