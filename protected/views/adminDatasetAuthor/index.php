<?php
$this->breadcrumbs=array(
	'Dataset Authors',
);

$this->menu=array(
	array('label'=>'Create DatasetAuthor', 'url'=>array('create')),
	array('label'=>'Manage DatasetAuthor', 'url'=>array('admin')),
);
?>

<h1>Dataset Authors</h1>

<?php $this->widget('zii.widgets.CListView', array(
	'dataProvider'=>$dataProvider,
	'itemView'=>'_view',
)); ?>
