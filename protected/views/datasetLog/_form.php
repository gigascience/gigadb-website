<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'dataset-log-form',
	'enableAjaxValidation'=>false,
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'dataset_id'); ?>
		<div class="controls">
			<?php echo $form->dropDownList($model, 'dataset_id', CHtml::listData(Util::getDois(), 'id', 'identifier'),array('style'=>'width:250px')); ?>
			<?php echo $form->error($model,'dataset_id'); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'message'); ?>
		<div class="controls">
			<?php echo $form->textArea($model,'message',array('rows'=>6, 'cols'=>50)); ?>
			<?php echo $form->error($model,'message'); ?>
		</div>
	</div>

	<div class="pull-right">
        		<a href="/datasetLog/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
    </div>
</div>