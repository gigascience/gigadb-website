<?php
$this->breadcrumbs=array(
	'Samples',
);

$this->menu=array(
	array('label'=>'Create Sample', 'url'=>array('create')),
	array('label'=>'Manage Sample', 'url'=>array('admin')),
);
?>

<h1>Samples</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
