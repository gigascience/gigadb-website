<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'View User #' . $model->id,
    'breadcrumbItems' => [
      ['label' => 'Admin', 'href' => '/site/admin'],
      ['label' => 'Manage', 'href' => '/user/admin'],
      ['isActive' => true, 'label' => 'View'],
    ]
  ]);

  $user_command = UserCommand::model()->findByAttributes(array("requester_id" => $model->id, "status" => "pending"));
  $linked_author = Author::findAttachedAuthorByUserId($model->id);

  $this->widget('zii.widgets.CDetailView', array(
    'data' => $model,
    'attributes' => array(
      'id',
      'email',
      'first_name',
      'last_name',
      'affiliation',
      'role',
      array(
        'label' => 'Is Activated',
        'value' => $model->is_activated ? 'Yes' : 'No'
      ),
      array(
        'label' => 'Is Receiving Newsletter',
        'value' => $model->newsletter ? 'Yes' : 'No'
      ),
    ),
    'htmlOptions' => array('class' => 'table table-striped table-bordered dataset-view-table'),
    'itemCssClass' => array('odd', 'even'),
    'itemTemplate' => '<tr class="{class}"><th scope="row">{label}</th><td>{value}</td></tr>'
  ));


  if (null != $user_command) {
  ?>
    <div class="alert alert-gigadb-info">
      <?php
      $claimed_author = Author::model()->findByPk($user_command->actionable_id);
      if (null != $claimed_author) {
        echo "This user has a pending claim on {$claimed_author->getDisplayName()} ({$claimed_author->id})";
      }
      echo CHtml::link(
        'Edit user to validate/reject the claim',
        array('user/update', 'id' => $model->id),
        array('class' => 'btn background-btn ml-10 mr-10')
      );
      ?>
    </div>
  <?php
  } else if (null !=  $linked_author) {
  ?>
    <div class="alert alert-gigadb-info">
      This user is linked to author: <? echo $linked_author->getDisplayName() ?> (<? echo $linked_author->id ?>)
    </div>
  <?php
  }
  ?>
</div>