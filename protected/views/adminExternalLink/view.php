
<h1>View ExternalLink #<?php echo $model->id; ?></h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= MyHtml::link('Manage External Links', array('admin')) ?>]
</div>
<? } ?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'dataset_id',
		'url',
		'external_link_type_id',
	),
)); ?>
