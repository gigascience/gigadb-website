<div class="container">
  <?php
    $this->widget('TitleBreadcrumb', [
        'pageTitle' => 'Update Dataset '.$model->identifier,
        'breadcrumbItems' => [
            ['label' => 'Admin', 'href' => '/site/admin'],
            ['href' => '/adminDataset/admin', 'label' => 'Manage'],
            ['isActive' => true, 'label' => 'Update'],
        ]
    ]);
  ?>
  <?php echo $this->renderPartial('_form', array('model'=>$model,'dataset_id'=>$dataset_id,'curationlog'=>$curationlog, 'datasetPageSettings' => $datasetPageSettings )); ?>

</div>
