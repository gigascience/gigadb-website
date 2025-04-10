<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Manage Users',
    'breadcrumbItems' => [
      ['label' => 'Admin', 'href' => '/site/admin'],
      ['isActive' => true, 'label' => 'Users'],
    ]
  ]);
  ?>
  <p>
    To list certain news items that you are looking for, you may search via keyword or value. Type your keyword or value into their respective boxes under the column headers and press the enter key. You may optionally enter a comparison operator (<b>&lt;</b>, <b>&lt;=</b>, <b>&gt;</b>, <b>&gt;=</b>, <b>&lt;&gt;</b>
    or <b>=</b>) at the beginning of each of your search values to specify how the comparison should be done.
  </p>

<?php
Yii::app()->clientScript->registerScript('customize-close-button', '
  $(document).on("dialogopen", "#controls", function(event, ui) {
    var closeButton = $(".ui-dialog-titlebar-close", $(this).parent());
    closeButton.html("<i class=\'fa fa-close fa-lg\'></i>");
    closeButton.attr("aria-label", "Close Dialog");
  });
');
?>

<?php $this->widget('CustomGridView', array(
  'id' => 'user-grid',
  'dataProvider' => $model->search(),
  'filter' => $model, // turn on/off filtering
  'rowHtmlOptionsExpression' => 'array("data-userid" => $data->id)',
  'itemsCssClass' => 'table table-bordered dataset-table-wide',
  'template' => '<div class="dataset-table-wide-container">{items}</div>{pager}',
  'columns' => array(
    'id',
    'email',
    'first_name',
    'last_name',
    'role',
    array(
      'name' => 'affiliation',
      'headerHtmlOptions' => array('style' => 'min-width:150px;'),
    ),
    'facebook_id',
    'twitter_id',
    'linkedin_id',
    'google_id',
    'username',
    array(
      'name' => 'is_activated',
      'value' => '($data->is_activated) ? "Yes" : "No"'
    ),
    array(
      'name' => 'newsletter',
      'value' => '$data->renderNewsletter()',
    ),
    array(
      'class' => 'CButtonColumn',
      'header' => "Actions",
      'headerHtmlOptions' => array('style' => 'min-width: 120px'),
      'template' => '{view}{update}{linkAuthor}{delete}',
      'buttons' => array(
        'view' => array(
            'imageUrl' => false,
            'label' => '',
            'options' => array(
                "title" => "View",
                "class" => "fa fa-eye fa-lg icon icon-view",
                "aria-label" => "View"
            ),
        ),
        'update' => array(
            'imageUrl' => false,
            'label' => '',
            'options' => array(
                "title" => "Update",
                "class" => "fa fa-pencil fa-lg icon icon-update",
                "aria-label" => "Update"
            ),
        ),
        'delete' => array(
            'imageUrl' => false,
            'label' => '',
            'options' => array(
                "title" => "Delete",
                "class" => "fa fa-trash fa-lg icon icon-delete",
                "aria-label" => "Delete"
            ),
        ),
        'linkAuthor' => array(
            'imageUrl' => false,
            'label' => '',
            'url' => 'Yii::app()->urlManager->createUrl("adminAuthor/prepareUserLink", array("user_id" => $data->id))',
            'options' => array(
                "title" => "Link to Author",
                "class" => "fa fa-link fa-lg icon icon-link",
                "aria-label" => "Link to Author",
            ),
        )
    ),
    )
  ),
)); ?>

</div>