<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Update FileFormat ' . $model->id,
    'breadcrumbItems' => [
      ['label' => 'Admin', 'href' => '/site/admin'],
      ['label' => 'Manage', 'href' => '/adminFileFormat/admin'],
      ['isActive' => true, 'label' => 'Update'],
    ]
  ]);
    ?>

  <?php echo $this->renderPartial('_form', array('model' => $model)); ?>
</div>