<h2>Update User <?=$model->id?></h2>
<div class="clear"></div>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= MyHtml::link('Manage Users', array('admin')) ?>]
</div>
<? } ?>

<?= $this->renderPartial('_form', array(
	'model'=>$model,
    'scenario'=>'update',
	'update'=>true
)) ?>