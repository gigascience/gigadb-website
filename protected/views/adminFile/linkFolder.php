<div class="container">
	<?php
	$this->widget('TitleBreadcrumb', [
		'pageTitle' => 'Link Temp File Folder',
		'breadcrumbItems' => [
			['label' => 'Admin', 'href' => '/site/admin'],
			['label' => 'Manage', 'href' => '/adminFile/admin'],
			['isActive' => true, 'label' => 'Link Temp File Folder'],
		]
	]);
	?>
	<div class="section form row">

		<div class="col-md-offset-3 col-md-6">
			<?php $form = $this->beginWidget('CActiveForm', array(
				'id' => 'file-form',
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
					'data' => Dataset::model()->findAll("1=1 order by identifier desc"),
					'valueField' => 'id',
					'textField' => 'identifier',
				],
			]);
			?>


			<?php
			$this->widget('application.components.controls.TextField', [
				'form' => $form,
				'model' => $model,
				'attributeName' => 'folder_name',
				'description' => 'input the detailed ftp address, for example: aspera.gigadb.org',
				'inputOptions' => [
					'required' => 'required',
					'aria-required' => 'true',
					'maxlength' => 100
				],
			]);
			?>

			<?php
			$this->widget('application.components.controls.TextField', [
				'form' => $form,
				'model' => $model,
				'attributeName' => 'username',
				'inputOptions' => [
					'required' => 'required',
					'aria-required' => 'true',
					'maxlength' => 100
				],
			]);
			?>

			<?php
			$this->widget('application.components.controls.PasswordField', [
				'form' => $form,
				'model' => $model,
				'attributeName' => 'password',
				'inputOptions' => [
					'required' => 'required',
					'aria-required' => 'true',
					'maxlength' => 100
				],
			]);
			?>

			<div class="pull-right">
				<a href="/adminFile/admin" class="btn background-btn-o">Cancel</a>
				<?php echo CHtml::submitButton('Link', array('class' => 'btn background-btn')); ?>
			</div>
			<?php $this->endWidget(); ?>

		</div>
	</div>
</div>