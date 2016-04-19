<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'funder-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form'),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'uri', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'uri',array()); ?>
			<?php echo $form->error($model,'uri'); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'primary_name_display', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'primary_name_display',array()); ?>
			<?php echo $form->error($model,'primary_name_display'); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'country', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'country',array()); ?>
			<?php echo $form->error($model,'country'); ?>
		</div>
	</div>

	<div class="row buttons">
		<a href="/funder/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->