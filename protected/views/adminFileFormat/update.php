<?php
$this->breadcrumbs=array(
	'File Formats'=>array('index'),
	$model->name=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List FileFormat', 'url'=>array('index')),
	array('label'=>'Create FileFormat', 'url'=>array('create')),
	array('label'=>'View FileFormat', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage FileFormat', 'url'=>array('admin')),
);
?>

<h1>Update FileFormat <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>