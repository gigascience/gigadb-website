<?php
$this->breadcrumbs=array(
	'Manuscripts'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Manuscript', 'url'=>array('index')),
	array('label'=>'Create Manuscript', 'url'=>array('create')),
	array('label'=>'Update Manuscript', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Manuscript', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Manuscript', 'url'=>array('admin')),
);
?>

<h1>View Manuscript #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'identifier',
		'pmid',
		'dataset_id',
	),
)); ?>
