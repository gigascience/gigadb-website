<h2>User List</h2>

<div class="actionBar">
[<?= CHtml::link('New User',array('create')); ?>]
[<?= CHtml::link('Manage User',array('admin')); ?>]
</div>

<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>

<div class="item">
<?php foreach($userList as $n=>$model): ?>
<?= CHtml::encode($model->id); ?>:
<?= CHtml::link($model->email,array('show', 'id'=>$model->id)); ?>
 (<?= CHtml::encode($model->email); ?>)
 (<strong><?= CHtml::encode($model->getRole()); ?></strong>)
<br/>

<?php endforeach; ?>
</div>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>
