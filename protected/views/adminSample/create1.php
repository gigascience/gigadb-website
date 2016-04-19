
<h1>Create Sample</h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= MyHtml::link('Manage Samples', array('admin')) ?>]
</div>
<? } ?>

<?php echo $this->renderPartial('_form1', array('model'=>$model)); ?>
