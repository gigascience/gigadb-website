<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('species_id')); ?>:</b>
	<?php echo CHtml::encode($data->species_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('attributes')); ?>:</b>
	<?php echo CHtml::encode($data->attributes); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('code')); ?>:</b>
	<?php echo CHtml::encode($data->code); ?>
	<br />


</div>