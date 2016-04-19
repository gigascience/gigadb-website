<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'images-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array(
        'class'=>'form-horizontal',
        'enctype'=>'multipart/form-data')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'location',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'location',array('size'=>45,'maxlength'=>45)); ?>
		<?php echo $form->error($model,'location'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'tag',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'tag',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'tag'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'url',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'url',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'url'); ?>
                </div>
	</div>

	<div class="control-group">
        <?php echo $form->labelEx($model,'image_upload',array('class'=>'control-label')); ?>
				<div class="controls">
        <?php echo $model->imageChooserField('image_upload'); ?>
        <?php echo $form->error($model,'image_upload'); ?>
                </div>
    </div>


	<div class="control-group">
		<?php echo $form->labelEx($model,'license',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textArea($model,'license',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'license'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'photographer',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'photographer',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'photographer'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'source',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'source',array('size'=>60,'maxlength'=>256)); ?>
		<?php echo $form->error($model,'source'); ?>
                </div>
	</div>

	<div class="pull-right">
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
    </div>
</div>
