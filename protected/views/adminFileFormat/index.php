<?php
$this->breadcrumbs=array(
	'File Formats',
);

$this->menu=array(
	array('label'=>'Create FileFormat', 'url'=>array('create')),
	array('label'=>'Manage FileFormat', 'url'=>array('admin')),
);
?>

<h1>File Formats</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
