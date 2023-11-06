<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Create Curation Log',
    'breadcrumbItems' => [
      ['label' => 'Admin', 'href' => '/site/admin'],
      ['href' => '/adminDataset/admin', 'label' => 'Manage'],
      ['label' => 'Dataset', 'href' => $this->createAbsoluteUrl(
          'adminDataset/update',
          $this->getActionParams('id'),
      )],
      ['isActive' => true, 'label' => 'Create'],
    ]
  ]);
    ?>

  <?php
    echo $this->renderPartial(
        '_form1',
        [
          'model' => $model,
          'dataset_id' => $dataset_id,
        ]
    );
    ?>

</div>