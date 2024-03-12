<nav class="dataset-submission-nav" aria-label="create dataset">
  <?php
  // Current request URL without query string
  $currentUrl = Yii::app()->request->getUrl();

  // Define links with their respective labels
  $links = [
    "/datasetSubmission/create1" => Yii::t('app', 'Study'),
    "/datasetSubmission/authorManagement/id/{$model->id}" => Yii::t('app', 'Author'),
    "/datasetSubmission/projectManagement/id/{$model->id}" => Yii::t('app', 'Project'),
    "/datasetSubmission/linkManagement/id/{$model->id}" => Yii::t('app', 'Link'),
    "/datasetSubmission/exLinkManagement/id/{$model->id}" => Yii::t('app', 'External Link'),
    "/datasetSubmission/relatedDoiManagement/id/{$model->id}" => Yii::t('app', 'Related Doi'),
    "/datasetSubmission/sampleManagement/id/{$model->id}" => Yii::t('app', 'Sample'),
  ];

  foreach ($links as $url => $label) {
    $isActive = $currentUrl == $url;
    $class = $isActive ? 'class="active sw-selected-btn"' : 'class="js-submit"';

    if ($isActive) {
      echo CHtml::tag('span', [
        'class' => 'active sw-selected-btn',
      ], $label);
    } else {
      echo CHtml::tag('a', [
        'href' => $url,
        'class' => 'js-submit',
      ], $label);
    }
  }
  ?>
</nav>