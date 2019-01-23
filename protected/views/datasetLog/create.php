<h1>Create Update Log</h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= CHtml::link('Manage Update Logs', array('admin')) ?>]
</div>
<? } ?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>