<?php
$this->breadcrumbs=array(
	'Types',
);

$this->menu=array(
	array('label'=>'Create Type', 'url'=>array('create')),
	array('label'=>'Manage Type', 'url'=>array('admin')),
);
?>

<h1>Types</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
