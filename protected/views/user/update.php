<h2>Update User <?=$model->id?></h2>
<div class="clear"></div>
<? if (Yii::app()->user->checkAccess('admin')) { ?>
<div class="actionBar">
[<?= MyHtml::link('Manage Users', array('admin')) ?>]
</div>
<? } ?>

<?php echo CHtml::link('Attach an author to this user', 
                          array('adminAuthor/admin', 'attach_user'=>$model->id),
                          array('class' => 'btn')); ?>


<?= $this->renderPartial('_form', array(
	'model'=>$model,
    'scenario'=>'update',
	'update'=>true
)) ?>