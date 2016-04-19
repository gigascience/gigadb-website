<?php
$this->breadcrumbs=array(
	'File Types'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List FileType', 'url'=>array('index')),
	array('label'=>'Create FileType', 'url'=>array('create')),
	array('label'=>'Update FileType', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete FileType', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage FileType', 'url'=>array('admin')),
);
?>

<h1>View FileType #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'description',
	),
)); ?>
