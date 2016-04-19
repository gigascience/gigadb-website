<?php
$this->breadcrumbs=array(
	'Funders',
);

$this->menu=array(
	array('label'=>'Create Funder', 'url'=>array('create')),
	array('label'=>'Manage Funder', 'url'=>array('admin')),
);
?>

<h1>Funders</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
