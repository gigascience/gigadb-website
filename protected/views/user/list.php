<h2>User List</h2>

<div class="actionBar">
[<?= MyHtml::link('New User',array('create')); ?>]
[<?= MyHtml::link('Manage User',array('admin')); ?>]
</div>

<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>

<div class="item">
<?php foreach($userList as $n=>$model): ?>
<?= MyHtml::encode($model->id); ?>:
<?= MyHtml::link($model->email,array('show', 'id'=>$model->id)); ?>
 (<?= MyHtml::encode($model->email); ?>)
 (<strong><?= MyHtml::encode($model->getRole()); ?></strong>)
<br/>

<?php endforeach; ?>
</div>
<br/>
<?php $this->widget('CLinkPager',array('pages'=>$pages)); ?>
