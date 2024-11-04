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

    $existingImageLocation = $model->image_location ?: null;
    $uploadLogoEndpoint = Yii::app()->createUrl('/adminProject/uploadTempLogo');
    $manifestPath = Yii::getAlias('js/vite-logo-upload-1.0.0/.vite/manifest.json');
    $manifest = [];

    if (file_exists($manifestPath)) {
      $manifest = json_decode(file_get_contents($manifestPath), true);
    }

    $entry = reset($manifest);
		?>

    <div
      id="vue-client_project-image-logo"
      data-image-location="<?php echo $existingImageLocation; ?>"
      data-endpoint="<?php echo $uploadLogoEndpoint; ?>"
      data-hidden-input-name="Project[image_location]"
    >
      <div class="spinner"></div>
    </div>
    <?php
    $isDev = true; // TODO use a way to detect if we are in dev or prod environment
    ?>
    <?php if ($isDev): ?>
      <script type="module" src="http://localhost:5173/@vite/client"></script>
      <script type="module" src="http://localhost:5173/src/main.ts"></script>
    <?php else: ?>
      <!-- this requires running the build command, i.e. (cd vite && npm run build) -->
      <!-- CSS for the entry point -->
      <?php if (isset($entry['css'])): ?>
        <?php foreach ($entry['css'] as $cssFile): ?>
          <link rel="stylesheet" href="/js/vite-logo-upload-1.0.0/<?= $cssFile ?>" />
        <?php endforeach; ?>
      <?php endif; ?>

      <!-- Main script file -->
      <?php if (isset($entry['file'])): ?>
        <script type="module" src="/js/vite-logo-upload-1.0.0/<?= $entry['file'] ?>"></script>
      <?php endif; ?>
    <?php endif; ?>

		<div class="pull-right btns-row">
			<a href="/adminProject/admin" class="btn background-btn-o">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class' => 'btn background-btn')); ?>
		</div>

		<?php $this->endWidget(); ?>
	</div>

</div>