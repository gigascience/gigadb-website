<?php
$cs = Yii::app()->getClientScript();
$cssCoreUrl = $cs->getCoreScriptUrl();
$cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js');
?>

<div class="row">
	<div class="span10 offset1 form well">
		
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'policy-form',
		'enableAjaxValidation'=>false,
		'htmlOptions'=>array('class'=>'form-horizontal',
							 'enctype'=>'multipart/form-data')
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
			<?php echo $form->labelEx($model,'until_date',array('class'=>'control-label')); ?>
			<div class="controls">
	        	<?php echo $form->textField($model, 'until_date', array('class'=>'date')); ?>
				<?php echo $form->error($model,'until_date'); ?>
			</div>
		</div>

		<div class="pull-right">
	        <a href="/site/admin" class="btn">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
		</div>

	<?php $this->endWidget(); ?>

	</div><!-- form -->
</div>
<script type="text/javascript">
$('.date').datepicker();
</script>
