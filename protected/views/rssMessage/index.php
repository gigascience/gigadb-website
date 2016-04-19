<?php
$this->breadcrumbs=array(
	'Rss Messages',
);

$this->menu=array(
	array('label'=>'Create RssMessage', 'url'=>array('create')),
	array('label'=>'Manage RssMessage', 'url'=>array('admin')),
);
?>

<h1>Rss Messages</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
