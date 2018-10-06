
<h1>Create Species</h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= CHtml::link('Manage Species', array('admin')) ?>]
</div>
<? } ?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
