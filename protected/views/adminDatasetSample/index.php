<?php
$this->breadcrumbs=array(
	'Dataset Samples',
);

$this->menu=array(
	array('label'=>'Create DatasetSample', 'url'=>array('create')),
	array('label'=>'Manage DatasetSample', 'url'=>array('admin')),
);
?>

<h1>Dataset Samples</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
