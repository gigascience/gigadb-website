<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('dataset_id')); ?>:</b>
	<?php echo CHtml::encode($data->dataset_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('sample_id')); ?>:</b>
	<?php echo CHtml::encode($data->sample_id); ?>
	<br />


</div>