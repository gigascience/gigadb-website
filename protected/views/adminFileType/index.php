<?php
$this->breadcrumbs=array(
	'File Types',
);

$this->menu=array(
	array('label'=>'Create FileType', 'url'=>array('create')),
	array('label'=>'Manage FileType', 'url'=>array('admin')),
);
?>

<h1>File Types</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
