<div class="container">
  <?php
    $this->widget('TitleBreadcrumb', [
      'pageTitle' => 'View Curation Log #' . $model->id,
      'breadcrumbItems' => [
        ['label' => 'Admin', 'href' => '/site/admin'],
        ['label' => 'Manage', 'href' => '/curationLog/admin'],
        ['isActive' => true, 'label' => 'View Log'],
      ]
    ]);

  $dataset = Dataset::model()->find('id=:dataset_id', [':dataset_id' => $model->dataset_id]);
  $this->widget(
      'zii.widgets.CDetailView',
      [
          'data'       => $model,
          'attributes' => [
              'id',
              [
                  'name'  => 'DataSet',
                  'value' => $dataset->identifier,
              ],
              'creation_date',
              'created_by',
              'last_modified_date',
              'last_modified_by',
              'action',
              'comments',
          ],
          'htmlOptions' => array('class' => 'table table-striped table-bordered dataset-view-table'),
          'itemCssClass' => array('odd', 'even'),
          'itemTemplate' => '<tr class="{class}"><th scope="row">{label}</th><td>{value}</td></tr>'
      ]
  );
  ?>
  <?php
  echo CHtml::link(
      'Back to this Dataset Curation Log',
      $this->createAbsoluteUrl(
          'adminDataset/update',
          ['id' => $model->dataset_id],
      )
  );
  ?>

</div>