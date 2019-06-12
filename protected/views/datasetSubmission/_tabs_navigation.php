<?php
$controller = Yii::app()->controller->id;
$action = Yii::app()->controller->action->id;
?>

<?php if ($model->is_test): ?>
    <div style="color: red;font-weight: bold;">This is ONLY for testing and cannot be submitted!</div>
<?php endif; ?>

<a href="<?= $model->getIsNewRecord() ? '#' : "/datasetSubmission/datasetManagement/id/{$model->id}" ?>"
   class="btn <?= $controller == 'datasetSubmission' && ($action == 'create1' || $action == 'datasetManagement') ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Study')?></a>
<a href="/datasetSubmission/authorManagement/id/<?= $model->id ?>"
   class="btn <?= $action == 'authorManagement' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Author')?></a>
<a href="/datasetSubmission/additionalManagement/id/<?= $model->id ?>"
   class="btn <?= $action == 'additionalManagement' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Additional Information')?></a>
<a href="/datasetSubmission/fundingManagement/id/<?= $model->id ?>"
   class="btn <?= $action == 'fundingManagement' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Funding')?></a>
<a href="/datasetSubmission/sampleManagement/id/<?= $model->id ?>"
   class="btn <?= $action == 'sampleManagement' || $action == 'end' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Sample')?></a>
<?php if( $controller == 'adminFile' && $action == 'create1' ): ?>
    <a href="/adminFile/create1/id/<?= $model->id ?>" class="btn <?= $action == 'create1' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'File')?></a>
<?php endif ?>

