<?php
$this->breadcrumbs=array(
	'Types'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Type', 'url'=>array('index')),
	array('label'=>'Create Type', 'url'=>array('create')),
	array('label'=>'View Type', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Type', 'url'=>array('admin')),
);
?>

<h1>Update Type <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>