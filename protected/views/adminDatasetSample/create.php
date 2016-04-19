<?php
$this->breadcrumbs=array(
	'Dataset Samples'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List DatasetSample', 'url'=>array('index')),
	array('label'=>'Manage DatasetSample', 'url'=>array('admin')),
);
?>

<h1>Create DatasetSample</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>