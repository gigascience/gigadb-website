<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Update Funder ' . $model->id,
    'breadcrumbItems' => [
      ['label' => 'Admin', 'href' => '/site/admin'],
      ['label' => 'Manage', 'href' => '/funder/admin'],
      ['isActive' => true, 'label' => 'Update'],
    ]
  ]);
  ?>

  <?php echo $this->renderPartial('_form', array('model' => $model)); ?>
</div>