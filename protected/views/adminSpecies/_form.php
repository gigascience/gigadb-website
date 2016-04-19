<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'species-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'tax_id',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'tax_id'); ?>
		<?php echo $form->error($model,'tax_id'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'common_name',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'common_name',array('size'=>60,'maxlength'=>64)); ?>
		<?php echo $form->error($model,'common_name'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'genbank_name',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'genbank_name',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'genbank_name'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'scientific_name',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'scientific_name',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'scientific_name'); ?>
                </div>
	</div>

	<div class="pull-right">
        <a href="/adminSpecies/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
    </div>
</div>
