<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'external-link-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'dataset_id',array('class'=>'control-label')); ?>
				<div class="controls">
        <?= CHtml::activeDropDownList($model,'dataset_id',CHtml::listData(Dataset::model()->findAll(),'id','identifier')); ?>
		<?php echo $form->error($model,'dataset_id'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'url',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'url',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'url'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'external_link_type_id',array('class'=>'control-label')); ?>
				<div class="controls">
        <?= CHtml::activeDropDownList($model,'external_link_type_id',CHtml::listData(ExternalLinkType::model()->findAll(),'id','name')); ?>
		<?php echo $form->error($model,'external_link_type_id'); ?>
                </div>
	</div>

	<div class="pull-right">
        <a href="/adminExternalLink/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
    </div>
</div>
