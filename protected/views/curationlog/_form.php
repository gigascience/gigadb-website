<?php
$cs = Yii::app()->getClientScript();
$cssCoreUrl = $cs->getCoreScriptUrl();
$cs->registerCssFile($cssCoreUrl . '/jui/css/base/jquery-ui.css');
Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js');
Yii::app()->clientScript->registerScriptFile('/js/jquery-migrate-1.2.1.js', CClientScript::POS_END);
?>

<div class="row">
	<div class="span10 offset1 form well">
		
	<?php $form=$this->beginWidget('CActiveForm', array(
		'id'=>'file-form',
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
			<?php echo $form->labelEx($model,'creation_date',array('class'=>'control-label')); ?>
			<div class="controls">
				<?php echo $form->textField($model,'creation_date' , array('class' => 'date')); ?>
				<?php echo $form->error($model,'creation_date'); ?>
			</div>
		</div>
                
               
		<div class="control-group">
			<?php echo $form->labelEx($model,'created_by',array('class'=>'control-label')); ?>
			<div class="controls">
				<?php echo $form->textField($model,'created_by',array('size'=>20,'maxlength'=>100)); ?>
				<?php echo $form->error($model,'created_by'); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model,'last_modified_date',array('class'=>'control-label')); ?>
			<div class="controls">
				<?php echo $form->textField($model,'last_modified_date', array('class' => 'date')); ?>
				<?php echo $form->error($model,'last_modified_date'); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model,'last_modified_by',array('class'=>'control-label')); ?>
			<div class="controls">
				<?php echo $form->textField($model,'last_modified_by',array('size'=>20,'maxlength'=>100)); ?>
				<?php echo $form->error($model,'last_modified_by'); ?>
			</div>
		</div>
                
                <div class="control-group">
			<?php echo $form->labelEx($model,'action',array('class'=>'control-label')); ?>
			<div class="controls">
				<?php echo $form->textField($model,'action',array('size'=>20,'maxlength'=>100)); ?>
				<?php echo $form->error($model,'action'); ?>
			</div>
		</div>

		<div class="control-group">
			<?php echo $form->labelEx($model,'comments',array('class'=>'control-label')); ?>
			<div class="controls">
				<?php echo $form->textArea($model,'comments',array('rows'=>6, 'cols'=>50)); ?>
				<?php echo $form->error($model,'comments'); ?>
			</div>
		</div>

		

		
		<div class="pull-right">
	        <a href="/curationLog/admin" class="btn">Cancel</a>
			<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
		</div>

	<?php $this->endWidget(); ?>



	</div><!-- form -->
</div>
<script type="text/javascript">
$('.date').datepicker({'dateFormat': 'yy-mm-dd'});
$('.btn-attr').click(function(e) {
	e.preventDefault();
	$('.js-new-attr').toggle();
})
$('.js-edit').click(function(e) {
	e.preventDefault();
	id = $(this).attr('data');
	
	row = $('.row-edit-'+id);
	if(id) {
		$.post('/adminFile/editAttr', {'id': id}, function(result) {
			if(result.success) {
				row.html(result.data);
				//$('.js-new-attr').remove();
			}
		}, 'json');
	}
})
</script>
