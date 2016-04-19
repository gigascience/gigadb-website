<h2>View User <?php echo $user->id; ?></h2>

<div class="actionBar">
[<?php echo MyHtml::link('New User',array('create')); ?>]
[<?php echo MyHtml::link('Update User',array('update','id'=>$user->id)); ?>]
[<?php echo MyHtml::linkButton('Delete User',array('submit'=>array('delete','id'=>$user->id),'confirm'=>'Are you sure?')); ?>
]
[<?php echo MyHtml::link('Manage User',array('admin')); ?>]
</div>

<table class="table table-bordered">
<tr>
	<th class="label"><?php echo MyHtml::encode($user->getAttributeLabel('email')); ?>
</th>
    <td><?php echo MyHtml::encode($user->email); ?>
</td>
</tr>

<tr>
	<th class="label"><?php echo MyHtml::encode($user->getAttributeLabel('first_name')); ?>
</th>
    <td><?php echo MyHtml::encode($user->first_name); ?>
</td>
</tr>

<tr>
	<th class="label"><?php echo MyHtml::encode($user->getAttributeLabel('last_name')); ?>
</th>
    <td><?php echo MyHtml::encode($user->last_name); ?>
</td>
</tr>

<tr>
	<th class="label"><?php echo MyHtml::encode($user->getAttributeLabel('role')); ?>
</th>
    <td><?php echo MyHtml::encode($user->role); ?>
</td>
</tr>

<tr>
	<th class="label"><?php echo MyHtml::encode($user->getAttributeLabel('newsletter')); ?>
</th>
    <td><?php echo $user->newsletter ? 'Yes' : 'No' ?>
</td>
</tr>

</table>
