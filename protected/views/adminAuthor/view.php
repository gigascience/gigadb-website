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
          echo CHtml::link('There is a pending claim on this author. Click for details', 
                                    array('AdminUserCommand/admin'),
                                    array('class' => 'btn'));
      }

?>