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

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'rss-message-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'message' ,array('class'=>'control-label') ); ?>
				<div class="controls">
		<?php echo $form->textField($model,'message',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'message'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'publication_date' ,array('class'=>'control-label') ); ?>
				<div class="controls">
		<?php echo $form->textField($model,'publication_date',array('class'=>'date')); ?>
		<?php echo $form->error($model,'publication_date'); ?>
                </div>
	</div>

	<div class="pull-right">
        <a href="/rssMessage/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
		<script>
		$('.date').datepicker();
		</script>
    </div>
</div>
