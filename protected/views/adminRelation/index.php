<?php
$this->breadcrumbs=array(
	'Relations',
);

$this->menu=array(
	array('label'=>'Create Relation', 'url'=>array('create')),
	array('label'=>'Manage Relation', 'url'=>array('admin')),
);
?>

<h1>Relations</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
