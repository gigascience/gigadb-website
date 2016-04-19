<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'file-format-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="row">
		<?php echo $form->labelEx($model,'name'); ?>
		<?php echo $form->textField($model,'name',array('size'=>10,'maxlength'=>10)); ?>
		<?php echo $form->error($model,'name'); ?>
	</div>

	<div class="row">
		<?php echo $form->labelEx($model,'description'); ?>
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
	</div>

	<div class="row buttons">
        <a href="/adminFileFormat/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save' , array('class' => 'btn-green')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
