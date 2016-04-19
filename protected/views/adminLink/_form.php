<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'link-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'dataset_id',array('class'=>'control-label')); ?>
				<div class="controls">
        <?= CHtml::activeDropDownList($model,'dataset_id',CHtml::listData(Util::getDois(),'id','identifier')); ?>
		<?php echo $form->error($model,'dataset_id'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'is_primary',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->checkBox($model,'is_primary'); ?>
		<?php echo $form->error($model,'is_primary'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'link',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'link',array('size'=>60,'maxlength'=>100)); ?>
		<?php echo $form->error($model,'link'); ?>
                </div>
	</div>

	<div class="pull-right">
        <a href="/adminLink/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
    </div>
</div>
