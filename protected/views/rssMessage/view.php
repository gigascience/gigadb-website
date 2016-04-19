
<h2>View RssMessage #<?php echo $model->id; ?></h2>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= MyHtml::link('Manage RSS Messages', array('admin')) ?>]
</div>
<? } ?>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'message',
		'publication_date',
	),
)); ?>
