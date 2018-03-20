<?php
$this->breadcrumbs=array(
	'Authors'=>array('index'),
	$model->name,
);

$this->menu=array(
	array('label'=>'List Author', 'url'=>array('index')),
	array('label'=>'Create Author', 'url'=>array('create')),
	array('label'=>'Update Author', 'url'=>array('update', 'id'=>$model->id)),
	array('label'=>'Delete Author', 'url'=>'#', 'linkOptions'=>array('submit'=>array('delete','id'=>$model->id),'confirm'=>'Are you sure you want to delete this item?')),
	array('label'=>'Manage Author', 'url'=>array('admin')),
);
?>

<?php
    $user_command = UserCommand::model()->findByAttributes(array("actionable_id" => $model->id, "action_label" => "claim_author"));
?>

<h1>View Author #<?php echo $model->id; ?></h1>

<?php $this->widget('zii.widgets.CDetailView', array(
	'data'=>$model,
	'attributes'=>array(
		'id',
		'name',
		'displayName',
		'orcid',
		'gigadb_user_id',
		'rank',
	),
)); ?>

<div class="clear"></div>
<?php
      if ( null != $user_command ) {
      	echo "<div class=\alert alert-info\">";
      	echo "There is a pending claim on this author.";
        echo CHtml::link('Edit user to validate/reject the claim', 
                                    array('user/update/','id' => $user_command->requester_id),
                                    array('class' => 'btn'));
        echo "</div>";
      }

      if (null != $model->gigadb_user_id) {
      	$user = User::model()->findByPk($model->gigadb_user_id);
      	if (null != $user) {
	      	echo "<div class=\"alert alert-info\">";
	      	echo "this author is linked to user {$user->first_name} {$user->last_name} ({$model->gigadb_user_id})";
	      	echo "</div>";
      	}
      }

?>