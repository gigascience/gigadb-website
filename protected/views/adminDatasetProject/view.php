<?php
$this->breadcrumbs=array(
	'Dataset Projects'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List DatasetProject', 'url'=>array('index')),
	array('label'=>'Create DatasetProject', 'url'=>array('create')),
	array('label'=>'Update DatasetProject', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete DatasetProject', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage DatasetProject', 'url'=>array('admin')),
);
?>

<h1>View DatasetProject #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'dataset_id',
		'project_id',
	),
)); ?>
