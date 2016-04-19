<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
		<div class="form">

		<?php $form=$this->beginWidget('CActiveForm', array(
			'id'=>'publisher-form',
			'enableAjaxValidation'=>false,
			'htmlOptions'=>array('class'=>'form-horizontal')
		)); ?>

			<p class="note">Fields with <span class="required">*</span> are required.</p>

			<?php echo $form->errorSummary($model); ?>

			<div class="control-group">
				<?php echo $form->labelEx($model,'name',array('class'=>'control-label')); ?>
				<div class="controls">
				<?php echo $form->textField($model,'name',array('size'=>45,'maxlength'=>45)); ?>
				<?php echo $form->error($model,'name'); ?>
				</div>
			</div>

			<div class="control-group">
				<?php echo $form->labelEx($model,'description',array('class'=>'control-label')); ?>
				<div class="controls">
				<?php echo $form->textArea($model,'description',array('rows'=>6, 'cols'=>50)); ?>
				<?php echo $form->error($model,'description'); ?>
				</div>
			</div>

			<div class="pull-right">
        <a href="/adminPublisher/admin" class="btn">Cancel</a>
				<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
			</div>

		<?php $this->endWidget(); ?>

		</div><!-- form -->
	</div>
</div>
