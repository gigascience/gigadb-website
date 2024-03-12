<div class="container dataset-submission-page">

  <?php
  $this->widget('TitleBreadcrumb', [
    'pageTitle' => 'Create Dataset',
    'breadcrumbItems' => []
  ]);

  ?>


  <?php
  $form = $this->beginWidget(
    'CActiveForm',
    array(
      'id' => 'dataset-form',
      'enableAjaxValidation' => false,
      'htmlOptions' => array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'
      ),
    )
  );
  ?>
  <nav class="dataset-submission-nav" aria-label="secondary">
    <a href="#" class="sw-selected-btn">
      <?= Yii::t('app', 'Study') ?>
    </a>
    <a href="/datasetSubmission/authorManagement/id/<?= $model->id ?>" class="js-submit">
      <?= Yii::t('app', 'Author') ?>
    </a>
    <a href="/datasetSubmission/projectManagement/id/<?= $model->id ?>" class="js-submit">
      <?= Yii::t('app', 'Project') ?>
    </a>
    <a href="/datasetSubmission/linkManagement/id/<?= $model->id ?>" class="js-submit">
      <?= Yii::t('app', 'Link') ?>
    </a>
    <a href="/datasetSubmission/exLinkManagement/id/<?= $model->id ?>" class="js-submit">
      <?= Yii::t('app', 'External Link') ?>
    </a>
    <a href="/datasetSubmission/relatedDoiManagement/id/<?= $model->id ?>" class="js-submit">
      <?= Yii::t('app', 'Related Doi') ?>
    </a>
    <a href="/datasetSubmission/sampleManagement/id/<?= $model->id ?>" class="js-submit">
      <?= Yii::t('app', 'Sample') ?>
    </a>
  </nav>

  <?
  $this->renderPartial('_form1', array('model' => $model, 'form' => $form, 'image' => $image));
  ?>


  <?php $this->endWidget(); ?>

</div>

<script>
  $('.js-submit').click(function (e) {
    e.preventDefault();
    $('#dataset-form').submit();
  });
</script>