<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
		<?  Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js'); ?>
		<div class="form">
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'prefix-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'prefix',array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'prefix',array('size'=>20,'maxlength'=>20)); ?>
			<?php echo $form->error($model,'prefix'); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'source',array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo CHtml::activeDropDownList($model,'source', array('EBI'=>'EBI', 'NCBI'=>'NCBI', 'DDBJ'=>'DDBJ'))?>
			<?php echo $form->error($model,'source'); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'url',array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textArea($model,'url',array('rows'=>3, 'cols'=>50)); ?>
			<?php echo $form->error($model,'url'); ?>
		</div>
	</div>

	<div class="pull-right">
        <a href="/adminLinkPrefix/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
	</div>
</div>
