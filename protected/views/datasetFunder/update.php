<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Update Dataset Funder ' . $model->id,
    'breadcrumbItems' => [
      ['label' => 'Admin', 'href' => '/site/admin'],
      ['label' => 'Manage', 'href' => '/datasetFunder/admin'],
      ['isActive' => true, 'label' => 'Update'],
    ]
  ]);
  ?>

  <?php echo $this->renderPartial('_form', array('model' => $model, 'datasets' => $datasets, 'funders' => $funders)); ?>
</div>