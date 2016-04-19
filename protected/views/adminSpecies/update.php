<?php
$this->breadcrumbs=array(
	'Species'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Species', 'url'=>array('index')),
	array('label'=>'Create Species', 'url'=>array('create')),
	array('label'=>'View Species', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Species', 'url'=>array('admin')),
);
?>

<h1>Update Species <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>