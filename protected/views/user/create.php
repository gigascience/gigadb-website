<div class="actionBar">
<? if (Yii::app()->user->checkAccess('manageUsers')) { ?>
[<?= MyHtml::link('Manage Users', array('admin')); ?>]
<? } ?>
</div>
<h2> <?=Yii::t('app' , 'Registration')?></h2>

<?= $this->renderPartial('_form', array(
	'model'=>$model,
    'scenario'=>'create',
	'update'=>false,
)) ?>

