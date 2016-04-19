<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('identifier')); ?>:</b>
	<?php echo CHtml::encode($data->identifier); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('pmid')); ?>:</b>
	<?php echo CHtml::encode($data->pmid); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('dataset_id')); ?>:</b>
	<?php echo CHtml::encode($data->dataset_id); ?>
	<br />


</div>