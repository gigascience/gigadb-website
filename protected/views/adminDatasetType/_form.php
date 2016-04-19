<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
		<?  Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js'); ?>
		<div class="form">
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'type-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'name',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'name',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'name'); ?>
				</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'description',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'description'); ?>
				</div>
	</div>

	<div class="pull-right">
        <a href="/adminDatasetType/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
	</div>
</div>
