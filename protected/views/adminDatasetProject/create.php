<?php
$this->breadcrumbs=array(
	'Dataset Projects'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List DatasetProject', 'url'=>array('index')),
	array('label'=>'Manage DatasetProject', 'url'=>array('admin')),
);
?>

<h1>Create DatasetProject</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>