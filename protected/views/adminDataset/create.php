<div class="container">
  <?php
    $this->widget('TitleBreadcrumb', [
        'pageTitle' => 'Create Dataset',
        'breadcrumbItems' => [
            ['label' => 'Admin', 'href' => '/site/admin'],
            ['href' => 'admin', 'label' => 'Manage'],
            ['isActive' => true, 'label' => 'Create'],
        ]
    ]);
    ?>
  <?php echo $this->renderPartial('_form', array('model'=>$model,'datasetPageSettings' => $datasetPageSettings )); ?>
</div>