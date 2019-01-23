<h2>Manage Your Dataset</h2>
<div class="clear"></div>

<?php
$form = $this->beginWidget('CActiveForm', array(
    'id' => 'dataset-form',
    'enableAjaxValidation' => false,
    'htmlOptions' => array(
        'class' => 'form-horizontal',
        'enctype' => 'multipart/form-data'),
        ));
?>

<a href="#" class="btn sw-selected-btn"><?= Yii::t('app' , 'Study')?></a>
<a href="/datasetSubmission/authorManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Author')?></a>
<a href="/datasetSubmission/projectManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Project')?></a>
<a href="/datasetSubmission/linkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Link')?></a>
<a href="/datasetSubmission/exLinkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'External Link')?></a>
<a href="/datasetSubmission/relatedDoiManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Related Doi')?></a>
<a href="/datasetSubmission/sampleManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Sample')?></a>
<? if($model->isProteomic) { ?>
<a href="/datasetSubmission/pxInfoManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'PX Info')?></a>
<? } ?>
<? if($model->files && count($model->files) > 0) { ?>
<a href="/adminFile/create1/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'File')?></a>
<? } ?>

<? 
    $this->renderPartial('_form1', array('model'=>$model, 'form'=>$form, 'image'=>$image));     
?>

<?php $this->endWidget(); ?>


