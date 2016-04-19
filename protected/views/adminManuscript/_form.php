<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'manuscript-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'identifier',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'identifier',array('size'=>32,'maxlength'=>32)); ?>
		<?php echo $form->error($model,'identifier'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'pmid',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'pmid'); ?>
		<?php echo $form->error($model,'pmid'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'dataset_id',array('class'=>'control-label')); ?>
				<div class="controls">
        <?= CHtml::activeDropDownList($model,'dataset_id',CHtml::listData(Util::getDois(),'id','identifier')); ?>
		<?php echo $form->error($model,'dataset_id'); ?>
                </div>
	</div>

	<div class="pull-right">
        <a href="/adminManuscript/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
    </div>
</div>
