<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
      'pageTitle' => 'Update Curation Log ' . $model->id,
      'breadcrumbItems' => [
        ['label' => 'Admin', 'href' => '/site/admin'],
        ['href' => '/adminDataset/admin', 'label' => 'Manage'],
        ['label' => 'Dataset', 'href' => $this->createAbsoluteUrl(
            'adminDataset/update',
            ['id' => $model->dataset_id],
        )],
        ['isActive' => true, 'label' => 'Update Log'],
        ]
    ]);
  echo $this->renderPartial('_form', ['model' => $model]);
  ?>
</div>