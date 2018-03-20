<h1>View User #<?php echo $model->id; ?></h1>

<?php
      $user_command = UserCommand::model()->findByAttributes(array("requester_id" => $model->id, "status" => "pending")) ;
      $linked_author = Author::findAttachedAuthorByUserId($model->id) ;

?>

<?php $this->widget('zii.widgets.CDetailView', array(
  'data'=>$model,
  'attributes'=>array(
    'id',
    'email',
    'first_name',
    'last_name',
    'affiliation',
    'role',
    array(
     'label'=>'Is Activated',
      'value'=> $model->is_activated ? 'Yes' : 'No'),
    array(
     'label'=>'Is Receiving Newsletter',
      'value'=> $model->newsletter ? 'Yes' : 'No'),
  ),
)); ?>

<div class="clear"></div>
<div class="alert alert-info">
<?php
      if ( null != $user_command ) {
          $claimed_author = Author::model()->findByPk($user_command->actionable_id);
          if (null != $user_command) {
            echo "<p>This user has a pending claim on {$claimed_author->getDisplayName()} ({$claimed_author->id})</p>";
          }
          echo CHtml::link('Edit user to validate/reject the claim', 
                                    array('user/update', 'id'=>$model->id),
                                    array('class' => 'btn'));
      }
      else {
?>
        This user is linked to author: <? echo $linked_author->getDisplayName() ?> (<? echo $linked_author->id ?>)
  </div>
<?php
      }
?>
