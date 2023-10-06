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

  <?php $this->beginWidget('zii.widgets.jui.CJuiDialog', array(
    'id' => 'controls',
    // additional javascript options for the dialog plugin
    'options' => array(
      'title' => 'Managing User',
      'autoOpen' => false,
      'modal' => true,
    ),
  ));

  ?>

  <h3>Basic Operations</h3>
  <a href="#" class="btn btn-active" title="view" onclick="goto_userview();">View</a>
  <a href="#" class="btn btn-active" title="update" onclick="goto_userupdate();">Update</a>
  <a href="#" class="btn btn-active delete" title="delete" onclick="goto_userdelete();">Delete</a>

  <h3>Advanced Operations</h3>
  <a href="#" class="btn btn-active delete" title="link" onclick="goto_userlinkauthor();">Link this user to an author</a>


  <div id="status"></div>

  <?

  $this->endWidget('zii.widgets.jui.CJuiDialog');

  ?>

  <?php $this->widget('zii.widgets.grid.CGridView', array(
    'id' => 'news-grid',
    'dataProvider' => $model->search(),
    'filter' => $model, // turn on/off filtering
    'itemsCssClass' => 'table table-bordered',
    'selectionChanged' => "function(id){open_controls($.fn.yiiGridView.getSelection(id));}",
    'columns' => array(
      'id',
      'email',
      'first_name',
      'last_name',
      'role',
      'affiliation',
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

      ),
    ),
  )); ?>
</div>

<script>
  function open_controls(user_id) {
    $("#controls").data('user_id', user_id);
    $("#controls").dialog("option", "title", "Manage User Id: " + user_id);
    $("#controls").dialog("open");
    return false;
  }

  function goto_userview() {
    <?
    echo 'var userview_url = "' . Yii::app()->urlManager->createUrl('user/view', array('id' => '')) . '";'
    ?>
    var user_id = $("#controls").data('user_id');
    window.location = userview_url + "/" + user_id;
  }

  function goto_userview() {
    <?
    echo 'var base_url = "' . Yii::app()->urlManager->createUrl('user/view', array('id' => '')) . '";'
    ?>
    var user_id = $("#controls").data('user_id');
    window.location = userview_url + "/" + user_id;
  }

  function goto_userview() {
    <?
    echo 'var base_url = "' . Yii::app()->urlManager->createUrl('user/view', array('id' => '')) . '";'
    ?>
    var user_id = $("#controls").data('user_id');
    window.location = base_url + "/" + user_id;
  }

  function goto_userupdate() {
    <?
    echo 'var base_url = "' . Yii::app()->urlManager->createUrl('user/update', array('id' => '')) . '";'
    ?>
    var user_id = $("#controls").data('user_id');
    window.location = base_url + "/" + user_id;
  }

  function goto_userdelete() {
    <?
    echo 'var base_url = "' . Yii::app()->urlManager->createUrl('user/delete', array('id' => '')) . '";'
    ?>
    var user_id = $("#controls").data('user_id');
    //window.location= base_url + "/" + user_id;
    $.ajax({
      url: base_url + "/" + user_id,
      type: 'POST',
      success: function(data) {
        $("#status").addClass("alert alert-success")
          .append("user successfully deactivated.")
          .append("Refresh the page to see the changes.");
      },
    });
  }

  function goto_userlinkauthor() {
    <?
    echo 'var base_url = "' . Yii::app()->urlManager->createUrl('adminAuthor/prepareUserLink', array('user_id' => '')) . '";'
    ?>
    var user_id = $("#controls").data('user_id');
    window.location = base_url + "/" + user_id;
  }
</script>