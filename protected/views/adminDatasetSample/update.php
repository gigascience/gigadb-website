<?php
$this->breadcrumbs=array(
	'Dataset Samples'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List DatasetSample', 'url'=>array('index')),
	array('label'=>'Create DatasetSample', 'url'=>array('create')),
	array('label'=>'View DatasetSample', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage DatasetSample', 'url'=>array('admin')),
);
?>

<h1>Update DatasetSample <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>