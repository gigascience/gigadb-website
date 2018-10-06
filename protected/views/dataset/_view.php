<div class="view">

	<b><?php echo CHtml::encode($data->getAttributeLabel('id')); ?>:</b>
	<?php echo CHtml::link(CHtml::encode($data->id), array('view', 'id'=>$data->id)); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('submitter_id')); ?>:</b>
	<?php echo CHtml::encode($data->submitter_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('image_id')); ?>:</b>
	<?php echo CHtml::encode($data->image_id); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('identifier')); ?>:</b>
	<?php echo CHtml::encode($data->identifier); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('title')); ?>:</b>
	<?php echo CHtml::encode($data->title); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('description')); ?>:</b>
	<?php echo CHtml::encode($data->description); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('publisher')); ?>:</b>
	<?php echo CHtml::encode($data->publisher_id); ?>
	<br />

	<?php /*
	<b><?php echo CHtml::encode($data->getAttributeLabel('dataset_size')); ?>:</b>
	<?php echo CHtml::encode($data->dataset_size); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('ftp_site')); ?>:</b>
	<?php echo CHtml::encode($data->ftp_site); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('upload_status')); ?>:</b>
	<?php echo CHtml::encode($data->upload_status); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('excelfile')); ?>:</b>
	<?php echo CHtml::encode($data->excelfile); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('excelfile_md5')); ?>:</b>
	<?php echo CHtml::encode($data->excelfile_md5); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('publication_date')); ?>:</b>
	<?php echo CHtml::encode($data->publication_date); ?>
	<br />

	<b><?php echo CHtml::encode($data->getAttributeLabel('modification_date')); ?>:</b>
	<?php echo CHtml::encode($data->modification_date); ?>
	<br />

	*/ ?>

</div>
<div class="clear"></div>
