<?php
$controller = Yii::app()->controller->id;
$action = Yii::app()->controller->action->id;

$modelId = $model->id ? '/id/' . $model->id : '';
$isTest = isset($_GET['is_test']) && $_GET['is_test'] == '1' ? '/is_test/1' : '';
?>

<?php if ($model->is_test): ?>
    <div style="color: red;font-weight: bold;">This is ONLY for testing and cannot be submitted!</div>
<?php endif; ?>

<a href="<?= $model->getIsNewRecord() ? '#' : "/datasetSubmission/datasetManagement{$modelId}{$isTest}" ?>"
   class="btn <?= $controller == 'datasetSubmission' && ($action == 'create1' || $action == 'datasetManagement') ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Study')?></a>
<a href="/datasetSubmission/authorManagement<?= $modelId . $isTest ?>"
   class="btn <?= $action == 'authorManagement' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Author')?></a>
<a href="/datasetSubmission/additionalManagement<?= $modelId . $isTest ?>"
   class="btn <?= $action == 'additionalManagement' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Additional Information')?></a>
<a href="/datasetSubmission/fundingManagement<?= $modelId . $isTest ?>"
   class="btn <?= $action == 'fundingManagement' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Funding')?></a>
<a href="/datasetSubmission/sampleManagement<?= $modelId . $isTest ?>"
   class="btn <?= $action == 'sampleManagement' || $action == 'end' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Sample')?></a>
<?php if( $model->upload_status == 'UserUploadingData' ): ?>
    <a href="/adminFile/create1<?= $modelId . $isTest ?>" class="btn <?= $controller == 'adminFile' && $action == 'create1' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'File')?></a>
<?php endif ?>

