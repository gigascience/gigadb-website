<?php
$this->breadcrumbs=array(
	'Images',
);

$this->menu=array(
	array('label'=>'Create Images', 'url'=>array('create')),
	array('label'=>'Manage Images', 'url'=>array('admin')),
);
?>

<h1>Images</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
