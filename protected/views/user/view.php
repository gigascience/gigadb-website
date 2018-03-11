<h1>View User #<?php echo $model->id; ?></h1>

<div class="clear"></div>

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
<?php if ( null != UserCommand::model()->findByAttributes(array("requester_id" => $model->id, "status" => "pending")) ) {
          echo CHtml::link('This user has a pending claim. Click for details', 
                                    array('AdminUserCommand/admin'),
                                    array('class' => 'btn'));
      }
      else if ( null == Author::findAttachedAuthorByUserId($model->id) ) {
          echo CHtml::link('Attach an author to this user', 
                                    array('adminAuthor/admin', 'attach_user'=>$model->id),
                                    array('class' => 'btn')); 
      }
?>
