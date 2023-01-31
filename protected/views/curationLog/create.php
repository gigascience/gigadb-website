<h1>Create Curation Log</h1>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= CHtml::link('Manage Logs', array('admin')) ?>]
</div>
<? } ?>

<?php echo $this->renderPartial('_form1', array('model'=>$model,'dataset_id'=>$dataset_id)); ?>

