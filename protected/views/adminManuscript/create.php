
<h1>Create Manuscript</h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= CHtml::link('Manage Manuscripts', array('admin')) ?>]
</div>
<? } ?>

<?php echo $this->renderPartial('_form', array('model'=>$model)); ?>
