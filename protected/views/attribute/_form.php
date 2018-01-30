<div class="form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'id'=>'attribute-form',
	'enableAjaxValidation'=>false,
	'htmlOptions'=>array('class'=>'form'),
)); ?>

	<p class="note">Fields with <span class="required">*</span> are required.</p>

	<?php echo $form->errorSummary($model); ?>

	<div class="control-group">
		<?php echo $form->labelEx($model,'attribute_name', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'attribute_name',array()); ?>
			<?php echo $form->error($model,'attribute_name'); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'definition', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'definition',array()); ?>
			<?php echo $form->error($model,'definition'); ?>
		</div>
	</div>

	<div class="control-group">
		<?php echo $form->labelEx($model,'model', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'model',array()); ?>
			<?php echo $form->error($model,'model'); ?>
		</div>
	</div>
        <div class="control-group">
		<?php echo $form->labelEx($model,'structured_comment_name', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'structured_comment_name',array()); ?>
			<?php echo $form->error($model,'structured_comment_name'); ?>
		</div>
	</div>
        <div class="control-group">
		<?php echo $form->labelEx($model,'value_syntax', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'value_syntax',array()); ?>
			<?php echo $form->error($model,'value_syntax'); ?>
		</div>
	</div>
        <div class="control-group">
		<?php echo $form->labelEx($model,'allowed_units', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'allowed_units',array()); ?>
			<?php echo $form->error($model,'allowed_units'); ?>
		</div>
	</div>
        <div class="control-group">
		<?php echo $form->labelEx($model,'occurance', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'occurance',array()); ?>
			<?php echo $form->error($model,'occurance'); ?>
		</div>
	</div>
                <div class="control-group">
		<?php echo $form->labelEx($model,'ontology_link', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'ontology_link',array()); ?>
			<?php echo $form->error($model,'ontology_link'); ?>
		</div>
	</div>

                <div class="control-group">
		<?php echo $form->labelEx($model,'note', array('class'=>'control-label')); ?>
		<div class="controls">
			<?php echo $form->textField($model,'note',array()); ?>
			<?php echo $form->error($model,'note'); ?>
		</div>
	</div>


	<div class="row buttons">
		<a href="/attribute/admin" class="btn">Cancel</a>
		<?php echo CHtml::submitButton($model->isNewRecord ? 'Create' : 'Save', array('class'=>'btn')); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- form -->