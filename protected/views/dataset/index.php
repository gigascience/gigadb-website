<?php
$this->breadcrumbs=array(
	'Datasets',
);

$this->menu=array(
	array('label'=>'Manage Dataset', 'url'=>array('admin')),
);
?>

<h1>Datasets</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
