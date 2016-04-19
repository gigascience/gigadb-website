<?php
$this->breadcrumbs=array(
	'Prefix'=>array('index'),
	$model->prefix=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Prefix', 'url'=>array('index')),
	array('label'=>'Create Prefix', 'url'=>array('create')),
	array('label'=>'View Prefix', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Prefix', 'url'=>array('admin')),
);

//$this->widget('zii.widgets.CBreadcrumbs', array(
//    'links'=>$this->breadcrumbs,
//));

?>



<h1>Update Prefix <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>