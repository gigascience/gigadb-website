<?php
$this->breadcrumbs=array(
	'Prefixes'=>array('index'),
	$model->prefix,
);

$this->menu=array(
	array('label'=>'List Prefix', 'url'=>array('index')),
	array('label'=>'Create Prefix', 'url'=>array('create')),
	array('label'=>'Update Prefix', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Prefix', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Prefix', 'url'=>array('admin')),
);
?>

<h1>View Prefix #<?php echo $model->id; ?></h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= MyHtml::link('Manage Prefixes', array('admin')) ?>]
</div>
<? } ?>
<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'prefix',
		'url',
	),
)); ?>
