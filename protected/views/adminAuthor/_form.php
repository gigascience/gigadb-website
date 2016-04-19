<div class="row">
	<div class="span8 offset2 form well">
		<div class="clear"></div>
		<?  Yii::app()->clientScript->registerScriptFile('/js/jquery-ui-1.8.21.custom.min.js'); ?>
<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'author-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form-horizontal')
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'surname',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'surname',array('size'=>60,'maxlength'=>255)); ?>
		<?php echo $form->error($model,'surname'); ?>
				</div>
	</div>

    <div class="control-group">
        <?php echo $form->labelEx($model,'first_name',array('class'=>'control-label')); ?>
        <div class="controls">
            <?php echo $form->textField($model,'first_name',array('size'=>60,'maxlength'=>255)); ?>
            <?php echo $form->error($model,'first_name'); ?>
        </div>
    </div>

    <div class="control-group">
        <?php echo $form->labelEx($model,'middle_name',array('class'=>'control-label')); ?>
        <div class="controls">
            <?php echo $form->textField($model,'middle_name',array('size'=>60,'maxlength'=>255)); ?>
            <?php echo $form->error($model,'middle_name'); ?>
        </div>
    </div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'orcid',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'orcid',array('size'=>60,'maxlength'=>128)); ?>
		<?php echo $form->error($model,'orcid'); ?>
				</div>
	</div>

    <?php /*
	<div class="control-group">
		<?php echo $form->labelEx($model,'rank',array('class'=>'control-label')); ?>
				<div class="controls">
		<?php echo $form->textField($model,'rank'); ?>
		<?php echo $form->error($model,'rank'); ?>
				</div>
	</div> */?>

	<div class="pull-right">
        <a href="/adminAuthor/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save',array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->
    </div>
</div>
