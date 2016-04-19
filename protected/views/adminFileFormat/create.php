<?php
$this->breadcrumbs=array(
	'File Formats'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List FileFormat', 'url'=>array('index')),
	array('label'=>'Manage FileFormat', 'url'=>array('admin')),
);
?>

<h1>Create FileFormat</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>