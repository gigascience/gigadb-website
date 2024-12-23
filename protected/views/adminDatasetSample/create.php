<div class="container">
  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Create DatasetSample',
    'breadcrumbItems' => [
      ['label' => 'Admin', 'href' => '/site/admin'],
      ['label' => 'Manage', 'href' => '/adminDatasetSample/admin'],
      ['isActive' => true, 'label' => 'Create'],
    ]
  ]);
  ?>

  <?php foreach (Yii::app()->user->getFlashes() as $key => $message): ?>
    <div role="alert" class="alert <?= $key === 'error' ? 'alert-danger' : 'alert-info' ?>">
      <div class="flash-<?= $key ?>"><?= $message ?></div>
    </div>
  <?php endforeach; ?>

  <?php echo $this->renderPartial('_form', array('model' => $model)); ?>
</div>