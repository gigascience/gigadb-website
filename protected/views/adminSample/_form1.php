<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'sample-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'species_id',array('class'=>'control-label')); ?>

				<div class="controls">
                                    
        <?= CHtml::activeDropDownList($model,'species_id',CHtml::listData(Species::model()->findAll(),'id','common_name')); ?>
		<?php echo $form->error($model,'species_id'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'s_attrs',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textArea($model,'s_attrs',array('rows'=>6, 'cols'=>50)); ?>
		<?php echo $form->error($model,'s_attrs'); ?>
                </div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'code',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'code',array('size'=>50,'maxlength'=>50)); ?>
		<?php echo $form->error($model,'code'); ?>
                </div>
	</div>

	<div class="pull-right">
        <a href="/adminDatasetSample/create1" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
                
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
    </div>
</div>


