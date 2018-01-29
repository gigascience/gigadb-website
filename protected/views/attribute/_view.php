<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('attribute_name')); ?>:</b>
	<?php echo CHtml::encode($data->attribute_name); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('definition')); ?>:</b>
	<?php echo CHtml::encode($data->definition); ?>
	<br />
        
        <b><?php echo CHtml::encode($data->getAttributeLabel('structured_comment_name')); ?>:</b>
	<?php echo CHtml::encode($data->structured_comment_name); ?>
	<br />



</div>