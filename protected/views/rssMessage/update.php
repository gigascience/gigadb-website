<?php
$this->breadcrumbs=array(
	'Rss Messages'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List RssMessage', 'url'=>array('index')),
	array('label'=>'Create RssMessage', 'url'=>array('create')),
	array('label'=>'View RssMessage', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage RssMessage', 'url'=>array('admin')),
);
?>

<h1>Update RssMessage <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>