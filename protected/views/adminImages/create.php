
<h1>Create Image</h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= CHtml::link('Manage Images', array('admin')) ?>]
</div>
<? } ?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
