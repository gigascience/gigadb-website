<?php
$action = Yii::app()->controller->action->id;
?>

<a href="<?= $model->getIsNewRecord() ? '#' : "/datasetSubmission/study/id/{$model->id}" ?>"
   class="btn <?= $action == 'study' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Study')?></a>
<a href="/datasetSubmission/author/id/<?= $model->id ?>"
   class="btn <?= $action == 'author' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Author')?></a>
<a href="/datasetSubmission/additional/id/<?= $model->id ?>"
   class="btn <?= $action == 'additional' ? 'sw-selected-btn' : 'nomargin' ?>"><?= Yii::t('app' , 'Additional Information')?></a>
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

