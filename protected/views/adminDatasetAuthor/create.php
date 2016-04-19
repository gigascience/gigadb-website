<?php
$this->breadcrumbs=array(
	'Dataset Authors'=>array('index'),
	'Create',
);

$this->menu=array(
	array('label'=>'List DatasetAuthor', 'url'=>array('index')),
	array('label'=>'Manage DatasetAuthor', 'url'=>array('admin')),
);
?>

<h1>Create DatasetAuthor</h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>