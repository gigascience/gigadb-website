<?php
$this->breadcrumbs=array(
	'Relations'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Relation', 'url'=>array('index')),
	array('label'=>'Create Relation', 'url'=>array('create')),
	array('label'=>'Update Relation', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Relation', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Relation', 'url'=>array('admin')),
);
?>

<h1>View Relation #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'dataset_id',
		'related_doi',
		//'relationship',
	),
)); ?>
