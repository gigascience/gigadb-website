<h1>View DatasetLog #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'dataset_id',
		array('label'=> 'DOI', 'value'=>$model->dataset->identifier),
		'message',
		'created_at',
		'model',
		'model_id',
	),
)); ?>
