
<h1>Create File</h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= CHtml::link('Manage Files', array('admin')) ?>]
</div>
<? } ?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
