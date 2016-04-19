<a href="/funder/admin">Back</a>
<h1>View Funder #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'uri',
		'primary_name_display',
		'country',
	),
)); ?>
