<?php
$this->breadcrumbs=array(
	'External Links'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List ExternalLink', 'url'=>array('index')),
	array('label'=>'Create ExternalLink', 'url'=>array('create')),
	array('label'=>'View ExternalLink', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage ExternalLink', 'url'=>array('admin')),
);
?>

<h1>Update ExternalLink <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>