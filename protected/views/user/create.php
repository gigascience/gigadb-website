<div class="actionBar">
<? if (Yii::app()->user->checkAccess('manageUsers')) { ?>
[<?= CHtml::link('Manage Users', array('admin')); ?>]
<? } ?>
</div>

<?= $this->renderPartial('_form', array(
	'model'=>$model,
    'scenario'=>'create',
	'update'=>false,
)) ?>

