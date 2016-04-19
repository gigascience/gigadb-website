<?php
$this->breadcrumbs=array(
	'Dataset Projects'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List DatasetProject', 'url'=>array('index')),
	array('label'=>'Create DatasetProject', 'url'=>array('create')),
	array('label'=>'View DatasetProject', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage DatasetProject', 'url'=>array('admin')),
);
?>

<h1>Update DatasetProject <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>