<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Create Curation Log',
    'breadcrumbItems' => [
      ['label' => 'Admin', 'href' => '/site/admin'],
      ['isActive' => true, 'label' => 'Create'],
    ]
  ]);
    ?>

  <?php if (Yii::app()->user->checkAccess('admin') === true) { ?>
    <div class="actionBar">
    <?php CHtml::link('Manage Logs', ['admin']); ?>
    </div>
  <?php } ?>

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