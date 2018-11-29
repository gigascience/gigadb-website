
<h2>File details</h2>
<div class="clear"></div>

<a href="/datasetSubmission/datasetManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Study')?></a>
<a href="/datasetSubmission/authorManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Author')?></a>
<a href="/datasetSubmission/projectManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Project')?></a>
<a href="/datasetSubmission/linkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Link')?></a>
<a href="/datasetSubmission/exLinkManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'External Link')?></a>
<a href="/datasetSubmission/relatedDoiManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Related Doi')?></a>
<a href="/datasetSubmission/sampleManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'Sample')?></a>
<? if($model->isProteomic) { ?>
<a href="/datasetSubmission/pxInfoManagement/id/<?= $model->id ?>" class="btn nomargin"><?= Yii::t('app' , 'PX Info')?></a>
<? } ?>
<a href="#" class="btn sw-selected-btn"><?= Yii::t('app' , 'File')?></a>

<?
$count = count($files);
if($count>0)
     echo $this->renderPartial('_form1', array('files'=>$files,'identifier'=>$identifier,
         'samples_data'=>$samples_data, 'dataset_id'=>$model->id));
else{
    
?>
<div class="span12 form well">
    <div class="form-horizontal">
        <div class="form overflow">
             <p>You can update the files when the administrator upload your files.</p>
             
               <div class="span12" style="text-align:center">                  
                <a href="/datasetSubmission/submit" class="btn-green">Submit</a>
            </div>
        </div>
    </div>
</div>

<?
}
?>

