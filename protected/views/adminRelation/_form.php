<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'relation-form',
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
		<?php echo $form->labelEx($model,'related_doi',array('class'=>'control-label')); ?>
				<div class="controls">
		<?= CHtml::activeDropDownList($model,'related_doi',CHtml::listData(Util::getDois(),'identifier','identifier')); ?>
		<?php echo $form->error($model,'related_doi'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'relationship',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php //echo $form->textField($model,'relationship',array('size'=>30,'maxlength'=>30)); ?>
		<?= CHtml::activeDropDownList($model, 'relationship_id',CHtml::listData(Relationship::model()->findAll(),'id','name')); ?>
		<?php echo $form->error($model,'relationship_id'); ?>
                </div>
	</div>

	<div class="pull-right">
        <a href="/adminRelation/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
    </div>
</div>
