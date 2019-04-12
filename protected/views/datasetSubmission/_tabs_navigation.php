<?php
$action = Yii::app()->controller->action->id;
?>

<a href="<?= $model->getIsNewRecord() ? '#' : "/datasetSubmission/study/id/{$model->id}" ?>"
   class="btn <?= $action == 'study' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Study')?></a>
<a href="/datasetSubmission/author/id/<?= $model->id ?>"
   class="btn <?= $action == 'author' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Author')?></a>
<a href="/datasetSubmission/projectManagement/id/<?= $model->id ?>"
   class="btn nomargin"><?= Yii::t('app' , 'Project')?></a>
<a href="/datasetSubmission/linkManagement/id/<?= $model->id ?>"
   class="btn nomargin"><?= Yii::t('app' , 'Link')?></a>
<a href="/datasetSubmission/exLinkManagement/id/<?= $model->id ?>"
   class="btn nomargin"><?= Yii::t('app' , 'External Link')?></a>
<a href="/datasetSubmission/relatedDoiManagement/id/<?= $model->id ?>"
   class="btn nomargin"><?= Yii::t('app' , 'Related Doi')?></a>
<a href="/datasetSubmission/sampleManagement/id/<?= $model->id ?>"
   class="btn nomargin"><?= Yii::t('app' , 'Sample')?></a>
<?php if($model->isProteomic) : ?>
    <a href="/datasetSubmission/pxInfoManagement/id/<?= $model->id ?>"
       class="btn nomargin"><?= Yii::t('app' , 'PX Info')?></a>
<?php endif; ?>
<?php if($model->files && count($model->files) > 0): ?>
    <a href="/adminFile/create1/id/<?= $model->id ?>"
       class="btn nomargin"><?= Yii::t('app' , 'File')?></a>
<?php endif; ?>

