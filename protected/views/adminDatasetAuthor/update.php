<div class="container">
  <?php
  $this->widget('application.components.TitleBreadcrumb', [
    'pageTitle' => 'Update DatasetAuthor' . $model->id,
    'breadcrumbItems' => [
      ['label' => 'Admin', 'href' => '/site/admin'],
      ['label' => 'Manage', 'href' => '/adminDatasetAuthor/admin'],
      ['isActive' => true, 'label' => 'Update'],
    ]
  ]);
  ?>

  <?php echo $this->renderPartial('_form', array('model' => $model)); ?>
</div>