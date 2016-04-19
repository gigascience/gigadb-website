<div class="wide form">

<?php $form=$this->beginWidget('CActiveForm', array(
	'action'=>Yii::app()->createUrl($this->route),
	'method'=>'get',
)); ?>

	<div class="row">
		<?php echo $form->label($model,'id'); ?>
		<?php echo $form->textField($model,'id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'dataset_id'); ?>
		<?php echo $form->textField($model,'dataset_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'funder_id'); ?>
		<?php echo $form->textField($model,'funder_id'); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'grant_award'); ?>
		<?php echo $form->textArea($model,'grant_award',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row">
		<?php echo $form->label($model,'comments'); ?>
		<?php echo $form->textArea($model,'comments',array('rows'=>6, 'cols'=>50)); ?>
	</div>

	<div class="row buttons">
		<?php echo CHtml::submitButton('Search'); ?>
	</div>

<?php $this->endWidget(); ?>

</div><!-- search-form -->