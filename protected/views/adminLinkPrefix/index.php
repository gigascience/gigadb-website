<?php
$this->breadcrumbs=array(
	'Prefixes',
);

$this->menu=array(
	array('label'=>'Create Prefix', 'url'=>array('create')),
	array('label'=>'Manage Prefix', 'url'=>array('admin')),
);
?>

<h1>Prefixes</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
