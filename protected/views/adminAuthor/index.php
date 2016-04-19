<?php
$this->breadcrumbs=array(
	'Authors',
);

$this->menu=array(
	array('label'=>'Create Author', 'url'=>array('create')),
	array('label'=>'Manage Author', 'url'=>array('admin')),
);
?>

<h1>Authors</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
