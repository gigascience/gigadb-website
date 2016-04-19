<?php
$this->breadcrumbs=array(
	'External Links',
);

$this->menu=array(
	array('label'=>'Create ExternalLink', 'url'=>array('create')),
	array('label'=>'Manage ExternalLink', 'url'=>array('admin')),
);
?>

<h1>External Links</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
