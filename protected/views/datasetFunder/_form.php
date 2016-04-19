<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'dataset-funder-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form'),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php //echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'dataset_id', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo CHtml::activeDropDownList($model,'dataset_id', $datasets); ?>
			<?php echo $form->error($model,'dataset_id'); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'funder_id', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo CHtml::activeDropDownList($model,'funder_id', $funders); ?>
			<?php echo $form->error($model,'funder_id'); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'grant_award', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textArea($model,'grant_award',array('rows'=>6, 'cols'=>50)); ?>
			<?php echo $form->error($model,'grant_award'); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'comments', array('class'=>'control-label')); ?>
		<div class="control">
			<?php echo $form->textArea($model,'comments',array('rows'=>6, 'cols'=>50)); ?>
			<?php echo $form->error($model,'comments'); ?>
		</div>
	</div>

	<div class="row buttons">
		<a href="/datasetFunder/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->