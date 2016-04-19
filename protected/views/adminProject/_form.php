<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'project-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'url',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'url',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'url'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'name',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'name',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'name'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'image_location',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'image_location',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'image_location'); ?>
                </div>
	</div>

	<div class="pull-right">
        <a href="/adminProject/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
    </div>
</div>
