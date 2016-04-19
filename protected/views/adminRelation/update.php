<?php
$this->breadcrumbs=array(
	'Relations'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Relation', 'url'=>array('index')),
	array('label'=>'Create Relation', 'url'=>array('create')),
	array('label'=>'View Relation', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Relation', 'url'=>array('admin')),
);
?>

<h1>Update Relation <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>