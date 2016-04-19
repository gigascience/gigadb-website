<?php
$this->breadcrumbs=array(
	'Species'=>array('index'),
	$model->id,
);

$this->menu=array(
	array('label'=>'List Species', 'url'=>array('index')),
	array('label'=>'Create Species', 'url'=>array('create')),
	array('label'=>'Update Species', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Species', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Species', 'url'=>array('admin')),
);
?>

<h1>View Species #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'tax_id',
		'common_name',
		'genbank_name',
		'scientific_name',
	),
)); ?>
