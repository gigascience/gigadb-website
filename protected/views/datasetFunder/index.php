<?php
$this->breadcrumbs=array(
	'Dataset Funders',
);

$this->menu=array(
	array('label'=>'Create DatasetFunder', 'url'=>array('create')),
	array('label'=>'Manage DatasetFunder', 'url'=>array('admin')),
);
?>

<h1>Dataset Funders</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
