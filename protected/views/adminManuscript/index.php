<?php
$this->breadcrumbs=array(
	'Manuscripts',
);

$this->menu=array(
	array('label'=>'Create Manuscript', 'url'=>array('create')),
	array('label'=>'Manage Manuscript', 'url'=>array('admin')),
);
?>

<h1>Manuscripts</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
