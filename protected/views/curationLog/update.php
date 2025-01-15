<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
      'pageTitle' => 'Update Curation Log ' . $model->id,
      'breadcrumbItems' => [
        ['label' => 'Admin', 'href' => '/site/admin'],
        ['href' => '/curationLog/admin', 'label' => 'Manage'],
        ['isActive' => true, 'label' => 'Update Log'],
        ]
    ]);
  echo $this->renderPartial('_form', ['model' => $model]);
  ?>
</div>