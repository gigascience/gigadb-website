<?php
$this->breadcrumbs=array(
	'Publishers'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Publisher', 'url'=>array('index')),
	array('label'=>'Create Publisher', 'url'=>array('create')),
	array('label'=>'Update Publisher', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Publisher', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Publisher', 'url'=>array('admin')),
);
?>

<h1>View Publisher #<?php echo $model->id; ?></h1>
<a href='/adminPublisher/admin'>[Manage Publishers]</a>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'description',
	),
)); ?>
