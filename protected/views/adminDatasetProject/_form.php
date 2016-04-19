<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'dataset-project-form',
	'enableAjaxValidation'=>false,
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
		<?php echo $form->labelEx($model,'project_id',array('class'=>'control-label')); ?>
				<div class="controls">
        <?= CHtml::activeDropDownList($model,'project_id',CHtml::listData(Project::model()->findAll(),'id','name')); ?>
		<?php echo $form->error($model,'project_id'); ?>
				</div>
	</div>

	<div class="row buttons">
        <a href="/adminDatasetProject/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
