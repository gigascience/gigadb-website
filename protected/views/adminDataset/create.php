<div class="container">
  <?php
    $this->widget('application.components.TitleBreadcrumb', [
        'pageTitle' => 'Create Dataset',
        'breadcrumbItems' => [
            ['label' => 'Datasets', 'href' => '/site/admin'],
            ['href' => 'admin', 'label' => 'Manage'],
            ['isActive' => true, 'label' => 'Create'],
        ]
    ]);
    ?>
  <div class="clear"></div>
  <?php echo $this->renderPartial('_form', array('model'=>$model,'datasetPageSettings' => $datasetPageSettings )); ?>
</div>