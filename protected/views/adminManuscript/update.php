<?php
$this->breadcrumbs=array(
	'Manuscripts'=>array('index'),
	$model->id=>array('view','id'=>$model->id),
	'Update',
);

$this->menu=array(
	array('label'=>'List Manuscript', 'url'=>array('index')),
	array('label'=>'Create Manuscript', 'url'=>array('create')),
	array('label'=>'View Manuscript', 'url'=>array('view', 'id'=>$model->id)),
	array('label'=>'Manage Manuscript', 'url'=>array('admin')),
);
?>

<h1>Update Manuscript <?php echo $model->id; ?></h1>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>