<?php
$this->breadcrumbs=array(
	'Dataset Projects',
);

$this->menu=array(
	array('label'=>'Create DatasetProject', 'url'=>array('create')),
	array('label'=>'Manage DatasetProject', 'url'=>array('admin')),
);
?>

<h1>Dataset Projects</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
