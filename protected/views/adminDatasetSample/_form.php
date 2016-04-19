<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'dataset-sample-form',
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
		<?php echo $form->labelEx($model,'sample_id',array('class'=>'control-label')); ?>
				<div class="controls">
        <?= CHtml::activeDropDownList($model,'sample_id',CHtml::listData(Sample::model()->findAll(),'id','id')); ?>
		<?php echo $form->error($model,'sample_id'); ?>
				</div>
	</div>

	<div class="row buttons">
        <a href="/adminDatasetSample/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
