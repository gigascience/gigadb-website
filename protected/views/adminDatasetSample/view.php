<?php
$this->breadcrumbs=array(
	'Dataset Samples'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List DatasetSample', 'url'=>array('index')),
	array('label'=>'Create DatasetSample', 'url'=>array('create')),
	array('label'=>'Update DatasetSample', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete DatasetSample', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage DatasetSample', 'url'=>array('admin')),
);
?>

<h1>View DatasetSample #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'dataset_id',
		'sample_id',
               	array('label'=>'Dataset Title', 'value'=>$model->dataset->title),
	),
)); ?>
