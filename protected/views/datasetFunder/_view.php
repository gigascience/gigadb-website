<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('dataset_id')); ?>:</b>
	<?php echo CHtml::encode($data->dataset->identifier); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('funder_id')); ?>:</b>
	<?php echo CHtml::encode($data->funder->primary_name_display); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('grant_award')); ?>:</b>
	<?php echo CHtml::encode($data->grant_award); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('comments')); ?>:</b>
	<?php echo CHtml::encode($data->comments); ?>
	<br />


</div>