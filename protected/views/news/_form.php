
<?php
$cs = Yii::app()->getClientScript();
$cssCoreUrl = $cs->getCoreScriptUrl();
$cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
?>
<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
		<?  Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js'); ?>
		<div class="form">


<div class="form">

		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'news-form',
			'enableAjaxValidation'=>false,
			'htmlOptions'=>array('class'=>'form-horizontal')
		)); ?>

			<p class="note">Fields with <span class="required">*</span> are required.</p>

			<?php echo $form->errorSummary($model); ?>

			<div class="control-group">
				<?php echo $form->labelEx($model,'title',array('class'=>'control-label')); ?>
				<div class="controls">
				<?php echo $form->textField($model,'title',array('size'=>60,'maxlength'=>200)); ?>
				<?php echo $form->error($model,'title'); ?>
				</div>
			</div>

			<div class="control-group">
				<?php echo $form->labelEx($model,'body',array('class'=>'control-label')); ?>
				<div class="controls">
				<?php echo $form->textArea($model,'body',array('rows'=>6, 'cols'=>50)); ?>
				<?php echo $form->error($model,'body'); ?>
				</div>
			</div>

			<div class="control-group">
				<?php echo $form->labelEx($model,'start_date',array('class'=>'control-label')); ?>
				<div class="controls">
				<?php echo $form->textField($model,'start_date',array('class'=>'date')); ?>
				<?php echo $form->error($model,'start_date'); ?>
				</div>
			</div>

			<div class="control-group">
				<?php echo $form->labelEx($model,'end_date',array('class'=>'control-label')); ?>
				<div class="controls">
				<?php echo $form->textField($model,'end_date',array('class'=>'date')); ?>
				<?php echo $form->error($model,'end_date'); ?>
				</div>
			</div>

			<div class="pull-right">
        <a href="/news/admin" class="btn">Cancel</a>
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
			</div>
			<div class="clear"></div>
		<?php $this->endWidget(); ?>

		</div><!-- form -->

		<script>
		$('.date').datepicker();
		</script>
	</div>
</div>
