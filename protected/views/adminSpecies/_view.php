<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('tax_id')); ?>:</b>
	<?php echo CHtml::encode($data->tax_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('common_name')); ?>:</b>
	<?php echo CHtml::encode($data->common_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('genbank_name')); ?>:</b>
	<?php echo CHtml::encode($data->genbank_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('scientific_name')); ?>:</b>
	<?php echo CHtml::encode($data->scientific_name); ?>
	<br />


</div>